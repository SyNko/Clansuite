<?php
?>

        <!-- Sidebar Navigation -->
        <div id="sidebar">
            <div id="jokerlogo">
                <img height="152" width="134" src="http://cdn.clansuite.com/images/clansuite-joker.gif" alt="Clansuite Joker Logo" title="Clansuite Joker Logo">
            </div>
            <div id="stepbar">
                <p><?php echo $language['MENU_HEADING']; ?></p>
                <?php
                for ($i = 1; $i <= $total_steps; $i++) {
                    if ($i < $step) {
                        $classValue = 'step-pass';
                    } elseif ($i == $step) {
                        $classValue = 'step-on';
                    } elseif ($i > $step) {
                        $classValue = 'step-off';
                    }

                    echo '<div class="'.$classValue.'">'. $language['MENUSTEP'.$i] . '</div>' . "\n\t\t";
                }
                ?>
            </div>
        </div>

    <!-- Installation Step Content -->
