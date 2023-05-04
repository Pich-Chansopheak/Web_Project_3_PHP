 <!-- alert Messages -->
    <?php 
        if($error != -1){
    ?>
        <div class="alert alert-<?= ($error == 1?"danger":"success")?> alert-dismissible" role="alert">
                <?= $errmsg?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
            </div>
    <?php
        }
    ?>
<!-- End of alert Messages -->