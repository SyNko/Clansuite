<?php

/**
 * Koch Framework
 * Jens-Andr� Koch � 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Datagrid\Columnrenderer;

/**
 * Datagrid Column Renderer Date
 *
 * Renders date cells.
 */
class Date extends ColumnRenderer implements ColumnRendererInterface
{
    /**
     * Date format
     * Default: d.m.Y => 13.03.2007
     *
     * @todo make it respect the dateFormat setting from config
     *
     * @var string
     */
    public $dateFormat = 'd.m.Y H:i';

    /**
     * Render the value(s) of a cell
     *
     *
     * @param Clansuite_Datagrid_Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $sDate = '';

        $oDatetime = date_create($oCell->getValue());

        if ($oDatetime !== false) {
            $sDate = $oDatetime->format($this->dateFormat);
        }

        return $sDate;
    }
}
