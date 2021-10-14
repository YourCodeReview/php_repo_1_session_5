<?php

namespace App\Entity;

use CodeIgniter\Entity\Entity;

class Base extends Entity
{

    protected $dates = [];

    protected $castHandlers = [
        'datedate' => 'App\Entity\Cast\DateDate',
        'datedatetime' => 'App\Entity\Cast\DateDateTime',
        'datedatetimesec' => 'App\Entity\Cast\DateDateTimeSec',
    ];

    /**
     * General method that will return all public and protected values
     * of this entity as an array. All values are accessed through the
     * __get() magic method so will have any casts, etc applied to them.
     *
     * @param boolean $onlyChanged If true, only return values that have changed since object creation
     * @param boolean $cast        If true, properties will be casted.
     * @param boolean $recursive   If true, inner entities will be casted as array as well.
     *
     * @return array
     */
    public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array
    {
        $res = parent::toArray($onlyChanged, $cast, $recursive);
        if (isset($this->outputattributes)) {
            $res = array_filter(
                $res,
                fn ($key) => in_array($key, $this->outputattributes),
                ARRAY_FILTER_USE_KEY
            );
        }
        return $res;
    }
}
