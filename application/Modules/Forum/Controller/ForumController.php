<?php

/**
 * Clansuite - just an eSports CMS
 * Jens-André Koch © 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
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

namespace Clansuite\Module;

/**
 * Clansuite_Module_Forum
 *
 * @category    Clansuite
 * @package     Modules
 * @subpackage  Forum
 */
class forum.module extends Controller
{
    private static $moduleInfos = array();

    public function _initializeModule()
    {
        $this->getModuleConfig();

        $moduleinfo = new Clansuite_ModuleInfoController();
        $modules_info_array = $moduleinfo->getModuleInformations( 'Forum' );
        array_pop($modules_info_array);

        foreach ($modules_info_array as $modules_info) {
            $infokey = strtolower($modules_info['name'].'_info');
            $packagekey = strtolower($modules_info['name'].'_package');

            self::$moduleInfos = array(
                'Modul'      => ucfirst($modules_info['name']),
                'Author'     => utf8_encode($modules_info['info'][$infokey]['author']),
                'Version'    => $modules_info['info'][$packagekey]['version']
            );
        }
    }

    /**
     * Display Categories and Boards
     * if exist only one category, will display the boards for this category widthout the category
     */
    public function action_show()
    {
        $subboards = array();

        // Set Pagetitle and Breadcrumbs
        Clansuite_Breadcrumb::add( _('Show'), '/forum/show');

        // Get Render Engine
        $view = $this->getView();

        $resultCategory = $this->getModel( 'Entities\ForumCategory' )->findAllCategories();
        #Clansuite_Debug::printR( $resultCategory );

        if ( count($resultCategory) >1 ) {
            $view->assign('withcat', true);
            $view->assign('categories', $resultCategory);
        } else {
            $view->assign('withcat', false);
            $resultBoards = $this->getModel( 'Entities\ForumBoards' )->findBoards();
            #Clansuite_Debug::printR( $resultBoards );

            foreach ($resultBoards as $board) {
                $aBoards = $board;
                $resultSubBoards = $this->getModel( 'Entities\ForumBoards' )->findSubBoards( $board['board_id'] );
                if ( count($resultSubBoards) >0 ) {
                    $aBoards['subb'] = 1;
                    foreach ($resultSubBoards as $sboard) {
                        $subboards[] = $sboard;
                    }
                    $aBoards['subboards'] = $subboards;
                    $aBoards['subboardscount'] = count($subboards);
                } else {
                    $aBoards['subb'] = 0;
                }
                $AllBoards[] = $aBoards;
            }

            #Clansuite_Debug::printR( $AllBoards );

            $view->assign('boards', $AllBoards);

            //unset( $AllBoards ); unset( $aBoards ); unset( $subboards ); unset( $resultSubBoards ); unset( $resultCategory );
        }

        $this->display();
    }

}
