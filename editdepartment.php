<?php
$memberFacultyID = $db->getColumnData("SELECT MemberFaculty FROM memberabout WHERE MemberID = ?", array($memberid));
?>
<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <select name="<?= $items->NavForm ?>" id="department_selectbox" class="form-select form-select-sm w-75 mx-auto">
            <option value="0" disabled selected><?= (is_null($memberFacultyID)) ? $translates["firstchoosefaculty"] : $items->NavOperation ?></option>
            <?php
            if (!is_null($memberFacultyID)) {
                $departments = $db->getDatas("SELECT * FROM departments_$language");
                foreach ($departments as $department) {
            ?>
                    <option value="<?= $department->DepartmentID; ?>"><?= $department->DepartmentName; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitabout" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>