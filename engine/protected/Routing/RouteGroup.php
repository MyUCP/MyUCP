<?php

class RouteGroup
{
    /**
     * Merge route groups into a new array.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    public static function merge($new, $old)
    {
        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        $new = array_merge(static::formatAs($new, $old), [
            'prefix' => static::formatPrefix($new, $old),
            'condition' => static::formatCondition($new, $old),
        ]);

        return array_merge_recursive(Arr::except(
            $old, ['prefix', 'as', 'condition']
        ), $new);
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatPrefix($new, $old)
    {
        $old = $old['prefix'] ?? null;
        return isset($new['prefix']) ? trim($old, '/').'/'.trim($new['prefix'], '/') : $old;
    }

    /**
     * Format the "as" clause of the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    protected static function formatAs($new, $old)
    {
        if (isset($old['as'])) {
            $new['as'] = $old['as'].($new['as'] ?? '');
        }
        return $new;
    }

    /**
     * Format the "condition" clause of the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    protected static function formatCondition($new, $old)
    {
        if (isset($old['condition'])) {
            $new['condition'] = $old['condition'];
        }

        return $new;
    }

    public static function validateDomain($regex, Request $request)
    {

    }
}