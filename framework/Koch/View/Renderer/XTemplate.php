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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for Xtemplate templates.
 *
 * This is a wrapper/adapter for rendering with XTemplate.
 *
 * @link http://www.phpxtemplate.org/ Offical Website of PHP XTemplate
 * @link http://xtpl.sourceforge.net/ Project's Website at Sourceforge
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Xtemplate extends AbstractRenderer
{
    public function __construct(Koch\Config $config)
    {
        parent::__construct($config);
    }

    public function initializeEngine($template = null)
    {
        // prevent redeclaration
        if (class_exists('XTemplate', false) == false) {
            // check if library exists
            if (is_file(ROOT_LIBRARIES . 'xtemplate/xtemplate.class.php') === true) {
                include ROOT_LIBRARIES . 'xtemplate/xtemplate.class.php';
            } else {
                throw new Exception('XTemplate Library missing!');
            }
        }

        $template = $this->getTemplatePath($template);

        #Koch_Debug::firebug('Xtemplate loaded with Template: ' . $template);

        // Do it with XTemplate style > eat like a bird, poop like an elefant!
        return $this->renderer = new XTemplate($template);
    }

    public function configureEngine()
    {

    }

    public function renderPartial($template)
    {

    }

    public function clearVars()
    {

    }

    public function clearCache()
    {

    }

    public function fetch($template, $data = null)
    {

    }

    public function display($template, $data = null)
    {

    }

    /**
     * Returns a clean xTemplate Object
     *
     * @return Smarty Object
     */
    public function getEngine()
    {
        // clear assigns?
        return $this->renderer;
    }

    public function render($template, $viewdata)
    {
        $this->renderer->assign($viewdata);
        $this->renderer->parse($template);
        $this->renderer->out($template);
    }

    public function assign($tpl_parameter, $value = null)
    {
        $this->renderer->assign($tpl_parameter, $value);
    }
}
