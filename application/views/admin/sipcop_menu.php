<?php foreach ($menu as $item) { ?>
    <?php if(count($item['subitems'])>0){ ?>
        <li class="sub-menu dcjq-parent-li">
            <a class="dcjq-parent " href="javascript:;">
                <i class="<?php echo $item['item']['ICONO']; ?>"></i> <span><?php echo $item['item']['NOMBRE']; ?></span>
            <span class="dcjq-icon"></span></a>
            <ul class="sub" style="display: block;">
                <?php foreach ($item['subitems'] as $subitem) { ?>
                    <li class="">
                        <a href="<?php echo $subitem['ENLACE']; ?>">
                            <i class="<?php echo $subitem['ICONO']; ?>"></i> <span><?php echo $subitem['NOMBRE']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php }else{ ?>
        <li>
            <a class="" href="<?php echo $item['item']['ENLACE']; ?>">
                <i class="<?php echo $item['item']['ICONO']; ?>"></i> <span><?php echo $item['item']['NOMBRE']; ?></span>
            </a>
        </li>
    <?php } ?>
<?php } ?>
