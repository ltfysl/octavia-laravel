/**
 * Word-level diff for prompt versions.
 *
 * Uses LCS on tokens (words + whitespace). Prompts are small (< a few
 * thousand words), so O(n·m) is fine; inputs are capped defensively and
 * fall back to line-level comparison beyond the cap.
 */

export type DiffPart = {
    type: 'same' | 'add' | 'remove';
    text: string;
};

const MAX_TOKENS = 1500;

/** Classic LCS length table. */
function lcsTable(a: string[], b: string[]): Uint32Array {
    const table = new Uint32Array((a.length + 1) * (b.length + 1));

    for (let i = 1; i <= a.length; i++) {
        for (let j = 1; j <= b.length; j++) {
            table[i * (b.length + 1) + j] =
                a[i - 1] === b[j - 1]
                    ? table[(i - 1) * (b.length + 1) + (j - 1)] + 1
                    : Math.max(
                          table[(i - 1) * (b.length + 1) + j],
                          table[i * (b.length + 1) + (j - 1)],
                      );
        }
    }

    return table;
}

function buildParts(a: string[], b: string[]): DiffPart[] {
    const table = lcsTable(a, b);
    const parts: DiffPart[] = [];

    const push = (type: DiffPart['type'], text: string) => {
        const last = parts[parts.length - 1];
        if (last && last.type === type) last.text += text;
        else parts.push({ type, text });
    };

    let i = a.length;
    let j = b.length;

    while (i > 0 && j > 0) {
        if (a[i - 1] === b[j - 1]) {
            push('same', a[i - 1]);
            i--;
            j--;
        } else if (table[(i - 1) * (b.length + 1) + j] >= table[i * (b.length + 1) + (j - 1)]) {
            push('remove', a[i - 1]);
            i--;
        } else {
            push('add', b[j - 1]);
            j--;
        }
    }

    while (i > 0) push('remove', a[--i]);
    while (j > 0) push('add', b[--j]);

    return parts.reverse();
}

/**
 * Diffs `oldText` against `newText`. Parts with type "add" exist only in
 * newText, "remove" only in oldText.
 */
export function diffWords(oldText: string, newText: string): DiffPart[] {
    if (oldText === newText) return [{ type: 'same', text: newText }];

    // Tokenize into words and whitespace; fall back to line-level for very
    // large prompts so the O(n·m) LCS stays responsive.
    let a = oldText.split(/(\s+)/).filter((t) => t !== '');
    let b = newText.split(/(\s+)/).filter((t) => t !== '');

    if (a.length > MAX_TOKENS || b.length > MAX_TOKENS) {
        a = oldText.split(/(?=\n)/);
        b = newText.split(/(?=\n)/);
    }

    return buildParts(a, b);
}

/** Added/removed word counts across the diff, for summary badges. */
export function diffStats(parts: DiffPart[]): { added: number; removed: number } {
    let added = 0;
    let removed = 0;

    for (const part of parts) {
        const words = part.text.trim() === '' ? 0 : part.text.trim().split(/\s+/).length;
        if (part.type === 'add') added += words;
        if (part.type === 'remove') removed += words;
    }

    return { added, removed };
}
