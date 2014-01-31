
<div class="container-fluid">
    <div class="overview">
        <div class="prose">
            <div class="row-fluid">
                <div class="span6">
                        <form class="form-horizontal" method="POST" action="<?php echo RELATIVEPATH; ?>manage/check">
                            <h2>Login</h2>
                            <?php
                            if ($login_status == "0") {
                                echo '<div class="alert alert-error">  
                                        <strong>Login failure!</strong> <br />
                                        You are either not a permitted user or your username/password is wrong.
                                      </div>';
                            } elseif ($login_status == "1") {
                                echo '<div class="alert alert-success">  
                                        <strong>Logged out</strong>
                                      </div>';
                            } elseif ($login_status == "2") {
                                echo '<div class="alert alert-error">  
                                        <strong>You are not logged in!  You must be logged in to view the page requested.</strong>
                                      </div>';
                            }
                            ?>
                            <div class="control-group">
                                <label class="control-label" for="inputEmail">Email</label>
                                <div class="controls">
                                    <input type="text" name="uEmail" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="inputPassword">Password</label>
                                <div class="controls">
                                    <input type="password" name="uPass" id="inputPassword" placeholder="Password">
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="btn">Sign in</button>
                                </div>
                            </div>        
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>