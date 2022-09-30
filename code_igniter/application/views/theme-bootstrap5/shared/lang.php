<?php
#  Copyright 2003-2015 Opmantek Limited (www.opmantek.com)
#
#  ALL CODE MODIFICATIONS MUST BE SENT TO CODE@OPMANTEK.COM
#
#  This file is part of Open-AudIT.
#
#  Open-AudIT is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published
#  by the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  Open-AudIT is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with Open-AudIT (most likely in a file named LICENSE).
#  If not, see <http://www.gnu.org/licenses/>
#
#  For further information on Open-AudIT or for a license other than AGPL please see
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************

/**
* @category  View
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_3.5.3
* @link      http://www.open-audit.org
 */
$GLOBALS['user_lang'] = 'en';
if (file_exists(APPPATH . 'views/lang/' . @$this->user->lang . '.inc')) {
    $GLOBALS['user_lang'] = $this->user->lang;
}

include APPPATH . 'views/lang/' . $GLOBALS['user_lang'] . '.inc';

if (!function_exists('__')) {
    function __($word)
    {
        // Learning-Mode
        // Only for Developers !!!!
        $language_learning_mode = 1;
        $language_file = APPPATH . 'views/lang/' . $GLOBALS['user_lang'] . '.inc';

        $word = (string) $word;

        if (isset($GLOBALS['lang'][$word])) {
            return $GLOBALS['lang'][$word];
        } else {
            // Learning-Mode
            if ($language_learning_mode === 1 && isset($word) && $word !== '') {
                if (is_writable($language_file)) {
                    // Deleting
                    $buffer = '';
                    $handle = fopen($language_file, 'r');
                    while (!feof($handle)) {
                        $line = fgets($handle, 4096);
                        if (!preg_match('/\?>/', $line)) {
                            $buffer .= $line;
                        }
                    }
                    fclose($handle);
                    // Writing new Variables
                    $handle = fopen($language_file, 'w+');
                    fwrite($handle, $buffer.''."\$GLOBALS[\"lang\"][\"$word\"]=\"$word\";\n?>");
                    fclose($handle);
                } else {
                    die("Language-Learning-Mode, but $language_file not writeable");
                }
            }

            return $word;
        }
    }
}