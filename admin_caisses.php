<form method="post" class="row g-2">
    <select name="utilisateur" class="form-select col">
        <?php
        $users = $pdo->query("SELECT id, nom FROM utilisateurs");
        foreach ($users as $u) {
            echo "<option value='{$u['id']}'>{$u['nom']}</option>";
        }
        ?>
    </select>

    <select name="role_dans_caisse" class="form-select col">
        <option value="directeur">directeur</option>
        <option value="commercial">Commercial</option>
        <option value="commercial">Animatrice</option>
    </select>

    <input type="hidden" name="id_caisse" value="<?= $caisse['id'] ?>">
    <button name="attribuer" class="btn btn-success">Attribuer</button>
</form>
