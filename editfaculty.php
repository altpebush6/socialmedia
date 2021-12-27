<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <select name="<?= $items->NavForm ?>" id="faculty_selectbox" class="form-select form-select-sm w-75 mx-auto shadow">
            <?php
            $memberFac = $db->getColumnData("SELECT MemberFaculty FROM memberabout WHERE MemberID = ?", array($memberid));
            $facName = $db->getColumnData("SELECT FacultyName FROM faculties_$language WHERE FacultyID = ?", array($memberFac));
            if ($memberFac) { ?>
                <option value="<?= $memberFac ?>" disabled selected><?= $facName ?></option>
            <?php } else { ?>
                <option value="0" disabled selected><?= $items->NavOperation ?></option>
            <?php }
            $faculties = $db->getDatas("SELECT * FROM faculties_$language");
            foreach ($faculties as $faculty) {
            ?>
                <option value="<?= $faculty->FacultyID; ?>"><?= $faculty->FacultyName; ?></option>
            <?php } ?>
        </select>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-theme submitabout shadow" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>