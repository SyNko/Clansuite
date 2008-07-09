<?php
/**
 * Security Handler
 */
if (!defined('IN_CS')){ die( 'Clansuite not loaded. Direct Access forbidden.' );}
?>
    <div id="sidebar">
        <div id="stepbar">
            <p><?=$language['MENU_HEADING']?></p>
            <div class="step-pass"><?=$language['MENUSTEP1']?> </div>
            <div class="step-pass"><?=$language['MENUSTEP2']?></div>
            <div class="step-pass"><?=$language['MENUSTEP3']?></div>
            <div class="step-on"><?=$language['MENUSTEP4']?></div>
            <div class="step-off"><?=$language['MENUSTEP5']?></div>
            <div class="step-off"><?=$language['MENUSTEP6']?></div>
            <div class="step-off"><?=$language['MENUSTEP7']?></div>
        </div>
    </div>
    <div id="content" class="narrowcolumn">
        <div id="content_middle">
            <div class="accordion">
                <h2 class="headerstyle">
                    <img src="images/64px-Document-save.svg.png" border="0" style="vertical-align:middle" alt="installstep image" />
                    <?=$language['STEP4_DATABASE']?>
                </h2>
                <?php if ($error != '' ) { ?>
                <fieldset class="error_red">
                    <legend>Error</legend>
                    <strong><?=$error ?></strong>
                </fieldset>
                <?php } ?>
                <p><?=$language['STEP4_SENTENCE1']; ?></p>
                <p><?=$language['STEP4_SENTENCE2']; ?></p>
                <p><?=$language['STEP4_SENTENCE3']; ?></p>
                <form action="index.php" method="post" accept-charset="UTF-8">
                    <fieldset>
                        <legend> Database Access Information</legend>
                        <input type="hidden" name="db_type" value="mysql" />
                        <ol class="formular">
                            <li>
                                <label class="formularleft" for="db_host"><?=$language['DB_HOST']?></label>
                                <input class="formularright" type="text" id="db_host" name="config[database][db_host]" value="<?=$values['db_host']?>" />
                            </li>
                            <li>
                                <label class="formularleft" for="db_type"><?=$language['DB_TYPE']?></label>
                                <input class="formularright" type="text" id="db_type" name="config[database][db_type]" value="<?=$values['db_type']?>" />
                            </li>
                            <li>
                                <label class="formularleft" for="db_username"><?=$language['DB_USERNAME']?></label>
                                <input class="formularright" type="text" id="db_username" name="config[database][db_username]" value="<?=$values['db_username']?>" />
                            </li>
                            <li>
                                <label class="formularleft" for="db_password"><?=$language['DB_PASSWORD']?></label>
                                <input class="formularright" type="text" id="db_password" name="config[database][db_password]" value="<?=$values['db_password']?>" />
                            </li>
                            <li>
                                <label class="formularleft" for="db_name"><?=$language['DB_NAME']?></label>
                                <input class="formularright" type="text" id="db_name" name="config[database][db_name]" value="<?=$values['db_name']?>" />
                            </li>
                            <li>
                                <label class="formularleft" for="db_create_database"><?=$language['DB_CREATE_DATABASE']?></label>
                                <input class="formularright" type="checkbox" id="db_create_database" name="config[database][db_create_database]"
                                <? if($values['db_create_database'] == '1') { ?> checked="checked" <? } ?> />
                            </li>
                            <li>
                                <label class="formularleft" for="db_prefix"><?=$language['DB_PREFIX']?></label>
                                <input class="formularright" type="text" id="db_prefix" name="config[database][db_prefix]" value="<?=$values['db_prefix']?>" />
                            </li>
                        </ol>
                    </fieldset>
                    <!--<p><?=$language['STEP4_SENTENCE4']; ?></p> -->
                    <!--<p><?=$language['STEP4_SENTENCE5']; ?></p> -->
                    <div id="content_footer">
                        <div class="navigation">
                            <span style="font-size:10px;">
                                <?=$language['CLICK_NEXT_TO_PROCEED']?><br />
                                <?=$language['CLICK_BACK_TO_RETURN']?>
                            </span>
                            <div class="alignright">
                                <input type="submit" value="<?=$language['NEXTSTEP']?>" class="ButtonGreen" name="step_forward" />
                            </div>
                            <div class="alignleft">
                                <input type="submit" value="<?=$language['BACKSTEP']?>" class="ButtonRed" name="step_backward" />
                                <input type="hidden" name="lang" value="<?=$_SESSION['lang']?>" />
                            </div>
                        </div><!-- div navigation end -->
                    </div> <!-- div content_footer end -->
                </form>
            </div> <!-- div accordion end -->
        </div> <!-- div content_middle end -->
    </div> <!-- div content end -->