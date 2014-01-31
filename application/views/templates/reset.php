        <div class="container">
            <?php
                if ($reset) {
                    echo '<div class="alert alert-success">Tables reset successfully.  All rows deleted.</div>';
                } else {
                    echo '<div class="alert alert-danger">Tables reset NOT successfuly.  Look around for the problem.</div>';
                }
            ?>
        </div>