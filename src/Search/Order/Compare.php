<?php

namespace AdinanCenci\FileEditor\Search\Order;

abstract class Compare
{
    /**
     * Compare two values.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    public static function compare($value1, $value2, $direction): int
    {
        if ($value1 == $value2) {
            return 0;
        }

        if (is_numeric($value1) && is_numeric($value2)) {
            return self::compareNumbers($value1, $value2, $direction);
        }

        if (is_string($value1) && is_string($value2)) {
            return self::compareStrings($value1, $value2, $direction);
        }

        if (is_array($value1) && is_array($value2)) {
            return self::compareArrays($value1, $value2, $direction);
        }

        if (is_null($value1) || is_null($value2)) {
            return self::compareNulls($value1, $value2, $direction);
        }

        return 0;
    }

    /**
     * Compare null values with other types.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    public static function compareNulls($value1, $value2, $direction): int
    {
        return $direction == 'ASC'
            ? (!is_null($value1) ?  1 : -1)
            : (!is_null($value1) ? -1 :  1);
    }

    /**
     * Compare numeric values.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    public static function compareNumbers($value1, $value2, $direction): int
    {
        return $direction == 'ASC'
            ? ($value1 > $value2 ?  1 : -1)
            : ($value1 > $value2 ? -1 :  1);
    }

    /**
     * Compare strings.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    public static function compareStrings($value1, $value2, $direction): int
    {
        $v = strcmp($value1, $value2);

        return $direction == 'ASC'
            ? ($v > 0 ?  1 : -1)
            : ($v > 0 ? -1 :  1);
    }

    /**
     * Compare arrays.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    public static function compareArrays($value1, $value2, $direction): int
    {
        $t1 = 0;
        $t2 = 0;

        while ($value1 || $value2) {
            $v1 = $value1 ? array_shift($value1) : null;
            $v2 = $value2 ? array_shift($value2) : null;

            $c = self::compare($v1, $v2, $direction);

            $t1 += $direction == 'ASC'  && $c > 0 ? 1 : 0;
            $t2 += $direction == 'DESC' && $c < 0 ? 1 : 0;
        }

        return $t1 == $t2
            ? 0
            : self::compareNumbers($t1, $t2, $direction);
    }
}
