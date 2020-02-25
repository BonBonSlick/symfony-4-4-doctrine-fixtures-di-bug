<?php declare(strict_types=1);

/*
 * Created by BonBonSlick
 */

namespace App\Entity\Core\Interfaces;


interface FactoryInterface
{
    /**
     * @param  $dto
     *
     * @return object
     */
    public function create($dto);
}
