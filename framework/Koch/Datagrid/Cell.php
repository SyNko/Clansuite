<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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

namespace Koch\Datagrid;

/**
 * Datagrid Cell
 *
 * Defines a cell within the datagrid
 */
class Cell extends Base
{
    /**
     * Value(s) of the cell
     * $_Values[0] is the standard value returned by getValue()
     *
     * @var array Mixed values
    */
    private $_Values = array();

    /**
     * Column object (Clansuite_Datagrid_Column)
     *
     * @var object Clansuite_Datagrid_Column
     */
    private $_columnObject;

    /**
     * Row object (Clansuite_Datagrid_Row)
     *
     * @var object Clansuite_Datagrid_Row
     */
    private $_Row;

    /**
     * Set the column object of this cell
     *
     * @param Clansuite_Datagrid_Column $_columnObject
     */
    public function setColumnObject($_columnObject)
    {
        $this->_columnObject = $_columnObject;
    }

    /**
     * Set the datagrid object
     *
     * @param Clansuite_Datagrid $_Datagrid
     */
    public function setjjjDatagrid($_Datagrid)
    {
        $this->_Datagrid = $_Datagrid;
    }

    /**
     * Set the row object of this cell
     *
     * @param Clansuite_Datagrid_Row $_Row
     */
    public function setRow($_Row)
    {
        $this->_Row = $_Row;
    }

    /**
     * Set the value of the cell
     *
     * @param mixed A single value ($_Value[0])
     */
    public function setValue($_Value)
    {
        $this->_Values[0] = $_Value;
    }

    /**
     * Set the values of the cell
     *
     * @param array Array of values
     */
    public function setValues($_Values)
    {
        $this->_Values = $_Values;
    }

    /**
     * Returns the column object of this cell
     *
     * @return Clansuite_Datagrid_Column $_columnObject
     */
    public function getColumn()
    {
        return $this->_columnObject;
    }

    /**
     * Get the Datagrid object
     *
     * @return Clansuite_Datagrid $_Datagrid
     */
    public function getDatagrid()
    {
        return $this->_Datagrid;
    }

    /**
     * Returns the row object of this cell
     *
     * @return Clansuite_Datagrid_Row $_Row
     */
    public function getRow()
    {
        return $this->_Row;
    }

    /**
     * Returns the value of this cell
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_Values[0];
    }

    /**
     * Returns the values of this cell
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_Values;
    }

    /**
     * Render the value
     *
     * @return string Returns the value (maybe manipulated by column cell renderer)
     */
    public function render()
    {
        return $this->getColumn()->renderCell($this);
    }
}
