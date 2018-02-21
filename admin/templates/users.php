<?php
    if( isset($_POST['submit']) ){
        $name = $_POST['user-name'];
        $password = $_POST['user-password'];
        $email = $_POST['user-email']; 
        $type = $_POST['user-type'];

        $data = array(
            'name'      => $name,
            'password'  => $password,
            'email'     => $email,
            'type'      => $type
        );
        Admin::registrateUser($data);
    }
?>

<div id="left">
    <p style="color: red;"><?= empty(Session::get('error-message'))?'':Session::get('error-message'); ?></p>
    <form action="" method="POST" id="registrate-user-form">
        <li>
            <label for="">Felhasználó neve:</label>
            <input type="text" name="user-name" required autocomplete="off" value="<?= !empty($name)?$name:'' ?>">
        </li>
        <li>
            <label for="">Felhasználó email címe:</label>
            <input type="email" name="user-email" required autocomplete="off">
        </li>
        <li>
            <label for="">Felhasználó jelszava:</label>
            <input type="text" name="user-password" required autocomplete="off">
            <button id="btn-generate-pass">Jelszó generálása</button>
        </li>
        <li>
            <label for="">Felhasználó típusa:</label>
            tanár<input type="radio" name="user-type" value="1">
            diák<input type="radio" name="user-type" value="0" checked>
        </li>
        <li>
            <input type="submit" name="submit" value="Felhasználó regisztrálása">
        </li>
    </form>

</div>
<div id="right">
    <div id="search-section">
        <input type="text" placeholder="keresés..." id="search-user">
    </div>

    <div id="users">
    <?php
        $users = Admin::getAllUsers();
        foreach( $users as $user ):
    ?>
        <li class="clear">
            <h4><?php echo $user->name; echo $user->type==0?' (diák)':' (tanár)' ?></h4>
            <span><?= empty($user->email)?'nincs megadva email cím':$user->email ?></span>
            <i class="ion-trash-a"></i>
        </li>
    <?php endforeach; ?>
    </div>
</div>