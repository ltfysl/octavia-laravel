<?php

namespace App\Services;

/**
 * Line-based diff using longest-common-subsequence dynamic programming.
 *
 * Prompt bodies are small (hard cap 20 000 chars per benchmark input),
 * so the O(n·m) table is acceptable and keeps the implementation free
 * of external diff dependencies.
 */
class DiffService
{
    public const OP_EQUAL = 'equal';

    public const OP_DELETE = 'delete';

    public const OP_INSERT = 'insert';

    /**
     * @param  string  $from  Old text
     * @param  string  $to  New text
     * @return array<int, array{op: string, text: string}>
     */
    public function lineDiff(string $from, string $to): array
    {
        $a = $this->splitLines($from);
        $b = $this->splitLines($to);

        $n = count($a);
        $m = count($b);

        // lcs[i][j] = LCS length of a[i..] and b[j..]
        $lcs = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
        for ($i = $n - 1; $i >= 0; $i--) {
            for ($j = $m - 1; $j >= 0; $j--) {
                $lcs[$i][$j] = $a[$i] === $b[$j]
                    ? $lcs[$i + 1][$j + 1] + 1
                    : max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
            }
        }

        $ops = [];
        $i = 0;
        $j = 0;
        while ($i < $n && $j < $m) {
            if ($a[$i] === $b[$j]) {
                $ops[] = ['op' => self::OP_EQUAL, 'text' => $a[$i]];
                $i++;
                $j++;
            } elseif ($lcs[$i + 1][$j] >= $lcs[$i][$j + 1]) {
                $ops[] = ['op' => self::OP_DELETE, 'text' => $a[$i]];
                $i++;
            } else {
                $ops[] = ['op' => self::OP_INSERT, 'text' => $b[$j]];
                $j++;
            }
        }
        for (; $i < $n; $i++) {
            $ops[] = ['op' => self::OP_DELETE, 'text' => $a[$i]];
        }
        for (; $j < $m; $j++) {
            $ops[] = ['op' => self::OP_INSERT, 'text' => $b[$j]];
        }

        return $ops;
    }

    /** @return list<string> */
    private function splitLines(string $text): array
    {
        if ($text === '') {
            return [];
        }

        return preg_split('/\R/', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
