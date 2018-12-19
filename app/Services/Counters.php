<?php

namespace App\Services;

use DB;

class Counters
{
    /**
     * Atomically increments counter value in database and returns it
     *
     * @param $name string Counter name to increment
     * @return int Incremented counter value
     * @throws \Throwable
     */
    public static function increment($name) {
        $value = -1;
        DB::transaction(function () use ($name, &$value) {
            $current = DB::selectOne("SELECT `value` FROM counters WHERE `name` = ? LIMIT 1 FOR UPDATE", [$name])->value;
            $updated = DB::update("UPDATE counters SET `value` = ? WHERE `name` = ? AND `value` = ?", [$current + 1, $name, $current]);
            if (!$updated) {
                // should never happen since we are in transaction and locked the row
                throw new \Exception("Failed to increment counter");
            }
            $value = $current + 1;
        });
        return $value;
    }
}
