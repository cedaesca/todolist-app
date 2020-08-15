<?php

/**
 * @see: https://stackoverflow.com/a/47070624/9409981
 */

namespace Tests\Traits;

trait AssertArrayStructure
{
    /**
     * Assert the array has a given structure.
     *
     * @param  array  $structure
     * @param  array  $arrayData
     * @return $this
     */
    public function assertArrayStructure(array $structure, array $arrayData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $arrayData);

                foreach ($arrayData as $arrayDataItem) {
                    $this->assertArrayStructure($structure['*'], $arrayDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $arrayData);

                $this->assertArrayStructure($structure[$key], $arrayData[$key]);
            } else {
                $this->assertArrayHasKey($value, $arrayData);
            }
        }

        return $this;
    }
}
