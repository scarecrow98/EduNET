<header class="content-header">
    <h2>Csoportok</h2>
    <?php if( IS_ADMIN ): ?>
    <button class="btn-rounded bg-2 modal-opener" data-modal-id="create-group" style="float: right;">
        <i class="ion-plus-round"></i>Csoport létrehozása
    </button>
    <?php endif; ?>
</header>
<section class="content-body flex-wrap">

        <?php
			$groups = Group::getAll(Session::get('user-id'), Session::get('user-type'));

            foreach($groups as $group):
        ?>
        <div class="group-box panel">
            <section style="background-image: url('<?php echo SERVER_ROOT.'uploads/avatars/'.$group->avatar ?>')">
                <div class="overlay">
                    <h3><?php echo $group->name ?></h3>
                    <span><?php echo $group->member_count ?> tag</span>
                </div>    
            </section>
            <div class="group-buttons">
                <button class="btn-view-members modal-opener" data-modal-id="view-members" data-group-id="<?php echo $group->id ?>">Csoporttagok megtekintése</button>
                <?php if( IS_ADMIN ): ?>
                <button class="btn-add-members bg-2 modal-opener" data-modal-id="add-members" data-group-id="<?php echo $group->id ?>">Csoporttagok felvétele</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

</section>
