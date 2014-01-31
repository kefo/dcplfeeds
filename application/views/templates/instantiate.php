        <div class="container">
            <?php
                if ($manage_table) {
                    echo '<div class="alert alert-info">Manage table created successfully.</div>';
                } else {
                    echo '<div class="alert alert-warning">Manage table NOT created. Problem.  Check settings</div>';
                }
                
                if ($data_table) {
                    echo '<div class="alert alert-info">Data table created successfully.</div>';
                } else {
                    echo '<div class="alert alert-warning">Data table NOT created. Problem.  Check settings</div>';
                }
                
                if ($manage_table && $data_table) {
                    echo '<div class="alert alert-success">Tables created successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger">Some tables NOT created. Problem. Check settings</div>';
                }
            ?>
        </div>