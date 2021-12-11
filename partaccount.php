<div class="row mt-2">
  <div class="col-12">
    <table class=" table table-light table-hover">
      <thead>
        <th></th>
        <th class="fs-4 p-3 text-center"><?= $translates["Accountset"] ?></th>
        <th></th>
      </thead>
      <?php
      $list = $db->getDatas("SELECT * FROM nav_account_$language");
      foreach ($list as $items) {
        switch ($items->NavID) {
          case 1:
            $contents_account = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = $memberid ");
            break;
          case 2:
            $contents_account = $db->getColumnData("SELECT MemberEmail FROM members WHERE MemberID = $memberid");
            break;
          case 3:
            $contents_account = $db->getColumnData("SELECT MemberPhone FROM memberabout WHERE MemberID = $memberid ");
            if ($contents_account[0] == "0") {
              $contents_account = "(" . substr($contents_account, 0, 4) . ")-" . substr($contents_account, 4, 3) . "-" . substr($contents_account, 7, 4);
            } else {
              $contents_account = "(0" . substr($contents_account, 0, 3) . ")-" . substr($contents_account, 3, 3) . "-" . substr($contents_account, 6, 4);
            }
            break;
          default:
            break;
        }
        $contents_account = ($contents_account == '' ? $translates["undefined"] : $contents_account);
      ?>
        <tr class="text-center border-bottom" id="<?= $items->NavLink ?>">
          <div class="row">
            <th class="py-3 col-3 border-end"><?= $items->NavName ?></th>
            <td class="py-3 col-6"><?= $contents_account ?></td>
            <?php if ($items->NavID == 2) { ?>
              <td class="py-3 col-3 border-start text-secondary"><?= $translates["cantchangemail"] ?></td>
            <?php } else { ?>
              <td class="edit-info py-3 col-3 border-start" onClick="EditOpen('<?= $items->NavLink ?>')"><?= $translates["edit"] ?> <i class="fas fa-pen" style="font-size:14px"></i></td>
            <?php } ?>
          </div>
        </tr>
        <tr class="text-center border-bottom d-none" id="edit_<?= $items->NavLink ?>">
          <form method="post" id="form_<?= $items->NavForm ?>">
            <div class="row">
              <th class="py-3 col-3 border-end"><?= $items->NavName ?></th>
              <td class="py-3 <?= ($items->NavID == 1) ? 'pb-4' : '' ?> col-6">
                <?php if ($items->NavID == 3) { ?>
                  <div class="input-group w-70 mx-auto">
                    <span class="input-group-text">
                      <img src="images/tr.png" class="rounded-1" width="30">
                    </span>
                    <input type="text" id="<?= $items->NavForm ?>_input" maxlength="11" class="form-control" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
                  </div>
                <?php } else { ?>
                  <div class="w-70 mx-auto">
                    <input type="text" id="<?= $items->NavForm ?>_input" class="form-control form-control-sm" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
                    <?= ($items->NavID == 1) ? '<div class="form-text text-start position-absolute" style="font-size:13px">' . $translates["sixmonths"] . '</div> ' : '' ?>
                  </div>
                <?php } ?>
              </td>
              <td class="py-3 col-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitaccount" onClick='SendFormAccount("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavLink ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavLink ?>_spinner"></span></button></td>
            </div>
          </form>
        </tr>
      <?php } ?>
      <tr class="text-center border-bottom">
        <th class="py-3 border-end"><?= $translates["delacc"] ?></th>
        <td class="py-3"><?= $translates["expdelaccount"] ?></td>
        <td class="py-3 border-start"><a class="text-dark text-decoration-none delacc" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#delUser"><?= $translates["del"] ?> <i class="fas fa-user-times" style="font-size:14px"></i></a></td>
      </tr>
    </table>
    <div id="account_result"></div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="delUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= $translates["deluser"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?= $translates["suretodeleteuser"] ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translates["no"] ?></button>
        <button type="button" class="btn btn-danger" onClick="RemoveMember('deletemember')"><?= $translates["yes"] ?></button>
      </div>
    </div>
  </div>
</div>