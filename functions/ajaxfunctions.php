<script>
  var SITE_URL = "http://localhost/aybu";
  var ID = <?php
            if ($memberid) {
              echo $memberid;
            } else {
              echo "none";
            }
            ?>;
  var Page = '<?php
              if ($page) {
                echo $page;
              } else {
                echo "0";
              }
              ?>';
  var Part = '<?php
              if ($part) {
                echo $part;
              } else {
                echo "0";
              }
              ?>';

  var GroupID = '<?php
                  if ($edit) {
                    echo $edit;
                  } else {
                    echo "0";
                  }
                  ?>';

  var pageNum = '<?php if ($pageNum) {
                    echo $_GET["pageNum"];
                  } else {
                    echo "0";
                  } ?>';

  var category = '<?php if ($category) {
                    echo $_GET["category"];
                  } else {
                    echo "0";
                  } ?>';

  var uni = '<?php if ($uni) {
                echo $_GET["uni"];
              } else {
                echo "0";
              } ?>';

  var price = '<?php if ($price) {
                  echo $_GET["price"];
                } else {
                  echo "0";
                } ?>';

  var order = '<?php if ($order) {
                  echo $_GET["order"];
                } else {
                  echo "0";
                } ?>';

  $("#allNotifications").click(function(e) {
    e.stopPropagation();
  })

  $(function() {

    $.controlLogin = function() {
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxcontrollogin.php",
        data: {
          "MemberID": ID
        },
        success: function(result) {
          if (result == 'problem') {
            window.location.href = SITE_URL + "/socialmedia/<?= $translates["login"] ?>";
          }
        }
      });
    }
  });
  setInterval('$.controlLogin()', 1000);

  // ANASAYFA AJAX

  $(function() {
    $("#form_posting").on('submit', function(e) {
      e.preventDefault();
      $("#spinnershare").html('<i class="fas fa-spinner fa-spin"></i>');
      $("#submitpost").prop("disabled", true);
      var Datas = new FormData(this);
      if (Page == '<?= $translates["clubs"] ?>') {
        Datas.append("ClubID", Part);
      } else {
        Datas.append("ClubID", 0);
      }
      Datas.append("text_post", $("#text_post").val());

      var filePath = $("#file_upload").val();
      var fileallowedExtensions = /(\.pdf|\.docx)$/i;

      var imagePath = $("#image_upload").val();
      var imageallowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;

      if (filePath && !fileallowedExtensions.exec(filePath)) {
        $("#warn_file").removeClass("d-none");
        $("#warn_file").addClass("d-block");
        $("#warn_file").html('<?= $translates["notallowedfile"] ?>');
        $("#spinnershare").html("");
        $("#submitpost").prop("disabled", false);
      } else if (imagePath && !imageallowedExtensions.exec(imagePath)) {
        $("#warn_file").removeClass("d-none");
        $("#warn_file").addClass("d-block");
        $("#warn_file").html('<?= $translates["notallowedimg"] ?>');
        $("#spinnershare").html("");
        $("#submitpost").prop("disabled", false);
      } else {
        $.ajax({
          type: "post",
          url: SITE_URL + "/socialmedia/ajaxposts.php?operation=posting",
          data: Datas,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          success: function(result) {
            $("#spinnershare").html("");
            $("#submitpost").prop("disabled", false);
            if (result.empty) {
              location.reload();
            } else if (result.error) {
              alert(result.error);
              location.reload();
            } else if (result.success) {
              location.reload();
            }
          }
        });
      }
    });

    $("#posts_container").on('submit', '.form_edit', function(e) { //Anasayfadaki postlarda edit
      e.preventDefault();
      $("#spinnereditpost").html('<i class="fas fa-spinner fa-spin"></i>');
      $(".saveedit").prop("disabled", true);
      var PostID = $(this).attr("idsi");
      var Datas = new FormData(this);
      Datas.append("edittedtext_post", $("#edittedtext_" + PostID).val());
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxposts.php?operation=editpost&PostID=" + PostID,
        data: Datas,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(result) {
          $("#spinnereditpost").html("");
          $(".saveedit").prop("disabled", false);
          $("#postmiddle_" + PostID).removeClass("d-none");
          $("#postmiddle_" + PostID).addClass("d-block");
          $("#likecomment_" + PostID).css("display", "flex");
          $("#addpartul_" + PostID).removeClass("d-block");
          $("#addpartul_" + PostID).addClass("d-none");
          $("#post_text_" + PostID).html(result.newText);
          if (result.newImages) {
            $("#post_images_" + PostID).html(result.newImages);
          }
          if (result.newFiles) {
            $("#post_files_" + PostID).html(result.newFiles);
            $("#edit_post_files_" + PostID).html(result.newFiles);
          }
        }
      });
    })

    // Search Box AJAX
    $("#search_person").keyup(function() {
      $("#persons").css("visibility", "visible", "fast");
      $("#persons").animate({
        opacity: "1"
      });
    })
    $("#search_person").focusout(function() {
      $("#persons").css("visibility", "hidden");
      $("#persons").animate({
        opacity: "0"
      }, "fast");
    })

    $("#search_person").on("keypress", function(e) {
      var person_info = $(this).val();
      if (e.which == 13) {
        var person_info = $(this).val();
        if (person_info != '') {
          $.ajax({
            url: SITE_URL + "/socialmedia/ajaxsearching.php",
            method: "post",
            dataType: "json",
            data: {
              search: person_info
            },
            success: function(result) {
              window.location.href = SITE_URL + "/socialmedia/<?= $translates['searchfriend'] ?>/" + result.key;
            }
          });
        }
      }
    })

    $("#search_person").keyup(function() {
      var person_info = $(this).val();
      if (person_info != '') {
        $.ajax({
          url: SITE_URL + "/socialmedia/ajaxsearching.php",
          method: "post",
          dataType: "json",
          data: {
            search: person_info,
            'Operation': 'headersearch'
          },
          success: function(result) {
            $("#searchfriendicon").attr("href", "http://localhost/aybu/socialmedia/<?= $translates['searchfriend'] ?>/" + result.key + "");
            if (!result.data) {
              $("#search_result").html("<a class='list-group-item text-dark' href='#'><?= $translates["noresult"] ?></a>");
            } else {
              $("#search_result").html(result.data);
              if (result.total >= 2) {
                $("#search_result").append("<a class='list-group-item text-dark' href='http://localhost/aybu/socialmedia/<?= $translates['searchfriend'] ?>/" + result.key + "'><?= $translates['showmore'] ?></a>");
              }
            }
          }
        });
      } else {
        $("#search_result").html("");
      }
    })

    $("#srchformsg").on("keyup", function() {
      var person_info = $(this).val();
      $.ajax({
        url: SITE_URL + "/socialmedia/ajaxsearching.php",
        method: "post",
        dataType: "json",
        data: {
          search: person_info,
          'Operation': 'messagessearch'
        },
        success: function(result) {
          $("#contactmain").html(result.data);
        }
      });
    })
    // Profil AJAX
    // Kapak Fotoğrafı
    $cover_image_crop = $('#cvr_image_demo').croppie({
      enableExif: true,
      viewport: {
        width: 200,
        height: 80,
        type: 'rectangular'
      },
      boundary: {
        width: 300,
        height: 300
      }
    });
    $("#upload_cvr_image").on("change", function() {
      var filePath = $(this).val();
      var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
      if (!allowedExtensions.exec(filePath) && filePath != "") {
        alert('Invalid file type');
        location.reload();
      } else {
        var reader = new FileReader();
        reader.onload = function(event) {
          $cover_image_crop.croppie('bind', {
            url: event.target.result
          }).then(function() {
            console.log("Jquery bind complete");
          });
        }
        reader.readAsDataURL(this.files[0]);
        $('#uploadcvrimageModal').removeClass("d-none");
        $('#uploadcvrimageModal').addClass("d-flex");
      }
    });
    $('.crop-image1').click(function(event) {
      $(this).prop("disabled", true);
      var filePath = $("#upload_cvr_image").val();
      var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
      if (!allowedExtensions.exec(filePath) && filePath != "") {
        alert('Invalid file type');
        location.reload();
      } else {
        $cover_image_crop.croppie('result', {
          type: 'canvas',
          size: 'viewport'
        }).then(function(response) {
          $.ajax({
            url: SITE_URL + "/socialmedia/ajaxpr_img.php?operation=uploadcoverimg&Names=<?= $user_name . '-' . $user_lastname ?>",
            type: "post",
            data: {
              "image1": response
            },
            dataType: "json",
            success: function(result) {
              $(this).prop("disabled", false);
              if (result.success) {
                $("#uploadcvrimageModal").html(result.success);
                location.reload();
              }
              if (result.error) {
                alert(result.error);
                $("#result_cv_img").html(result.error);
                $("#result_cv_hr").css("display", "block");
                $("#result_cv_img").css("display", "block");
                $("#result_cv_img").addClass("py-2 px-4");
              }
            }
          });
        })
      }
    });
    // Profile Fotoğrafı
    $image_crop = $('#image_demo').croppie({
      enableExif: true,
      viewport: {
        width: 200,
        height: 200,
        type: 'circle'
      },
      boundary: {
        width: 300,
        height: 300
      }
    });
    $("#upload_image").on("change", function() {
      var filePath = $(this).val();
      var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
      if (!allowedExtensions.exec(filePath) && filePath != "") {
        alert('Invalid file type');
        location.reload();
      } else {
        var reader = new FileReader();
        reader.onload = function(event) {
          $image_crop.croppie('bind', {
            url: event.target.result
          }).then(function() {
            console.log("Jquery bind complete");
          });
        }
        reader.readAsDataURL(this.files[0]);
        $('#uploadimageModal').removeClass("d-none");
        $('#uploadimageModal').addClass("d-flex");
      }
    });

    $('.crop-image2').click(function(event) {
      $(this).prop("disabled", true);
      var filePath = $("#upload_image").val();
      var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
      if (!allowedExtensions.exec(filePath) && filePath != "") {
        alert('Invalid file type');
        location.reload();
      } else {
        $image_crop.croppie('result', {
          type: 'canvas',
          size: 'viewport'
        }).then(function(response) {
          $.ajax({
            url: SITE_URL + "/socialmedia/ajaxpr_img.php?operation=uploadprofileimg&Names=<?= $user_name . '-' . $user_lastname ?>",
            type: "post",
            data: {
              "image2": response
            },
            dataType: "json",
            success: function(result) {
              $(this).prop("disabled", false);
              if (result.success) {
                $("#uploadimageModal").html(result.success);
                location.reload();
              }
              if (result.error) {
                alert(result.error);
                $("#result_pr_img").html(result.error);
                $("#result_pr_hr").css("display", "block");
                $("#result_pr_img").css("display", "block");
                $("#result_pr_img").addClass("py-2 px-4");
              }
            }
          });
        })
      }
    });

    $("#messageText").on("keypress", function(e) {
      if (e.which == 13) {
        e.preventDefault();
        $("#sendMessageBtn").trigger("click");
      }
    })
  }); // JQUERY BİTİŞ

  function FriendButton(Operation, UserID) {
    $.ajax({
      url: SITE_URL + "/socialmedia/ajaxaddfriend.php?operation=" + Operation,
      type: "post",
      dataType: "json",
      data: {
        "MemberID": ID,
        UserID
      },
      success: function(result) {
        location.reload();
      }
    });
  }

  function FriendAcceptment(Operation, PersonID, FriendID) {
    var Datas = {
      'FriendID': FriendID,
      'UserID': PersonID
    };
    $.ajax({
      url: SITE_URL + "/socialmedia/ajaxaddfriend.php?operation=" + Operation,
      type: "post",
      dataType: "json",
      data: Datas,
      success: function(result) {
        $("#noti_count").html(result.countnoti);
        if (result.countnoti == 0) {
          $(".friend_requests_noti").html('<li style="list-style-type: none;" class="p-1 text-center nonoti"><?= $translates["nonoti"] ?></li>');
        }
        if (Operation == 'accept') {
          $("#friends_exist").prepend(result.success);
          $(".friend_count").html(result.friend_count);
          $('#no_friends').remove();
        }
        if (result.norequest != 'null') {
          $(".friend_requests").html(result.norequest);
        } else {
          $(".friend_request_count").html(result.request_count);
        }
        $(".each_request_" + FriendID).remove();
      }
    });
  }


  function Like(Operation, PostID) {
    event.preventDefault();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?operation=" + Operation,
      data: {
        'ID': ID,
        PostID
      },
      dataType: "json",
      success: function(result) {
        $("#like_" + PostID).html(result.like);
      }
    });
  }

  function Comment(Operation, PostID) {
    event.preventDefault();
    var FormData = $("form#form_comment_" + PostID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?ID=" + ID + "&PostID=" + PostID + "&operation=" + Operation,
      data: FormData,
      dataType: "json",
      success: function(result) {
        if (result.success == "ok") {
          $("form").trigger("reset");
          $("#comment_label_" + PostID).html("<?= $translates["commentpost"] ?> (" + result.commentcounter + ")");
          $("#comments_" + PostID).append(result.comment);
        }
      }
    });
  }

  function CommentOperate(Operation, PostID, CommentID) {
    event.preventDefault();
    var FormData = $("#form_editcomment_" + CommentID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?ID=" + ID + "&PostID=" + PostID + "&CommentID=" + CommentID + "&operation=" + Operation,
      data: FormData,
      success: function(result) {
        if (Operation == 'editComment') {
          if (result == 'error') {
            location.reload();
          } else {
            $("#form_editcomment_" + CommentID).removeClass("d-block");
            $("#form_editcomment_" + CommentID).addClass("d-none");
            $("#comment_text_" + CommentID).css("display", "block");
            $("#comment_text_" + CommentID).html(result);
          }
        } else if (Operation == 'deleteComment') {
          $("#each_comment_" + CommentID).remove();
          $("#comment_label_" + PostID).html("<?= $translates["commentpost"] ?> (" + result + ")");
        }
      }
    });
  }

  $("#posts_container").on("click", ".reportPost", function() {
    var PostID = $(this).attr("postid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?PostID=" + PostID + "&operation=repPost",
      success: function(result) {
        $("#Report_Post_" + PostID).html('<i class="fas fa-headset"></i> ' + result);
        $("#Report_Post_" + PostID).removeClass("reportPost");
        $("#Report_Post_" + PostID).addClass("unreportPost");
        $("#Report_Post_" + PostID).removeClass("text-danger");
        $("#Report_Post_" + PostID).addClass("text-success");
      }
    });
  });
  $("#posts_container").on("click", ".unreportPost", function() {
    var PostID = $(this).attr("postid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?PostID=" + PostID + "&operation=delrepPost",
      success: function(result) {
        $("#Report_Post_" + PostID).html('<i class="fas fa-bug"></i> <?= $translates["reportpost"] ?>');
        $("#Report_Post_" + PostID).removeClass("unreportPost");
        $("#Report_Post_" + PostID).addClass("reportPost");
        $("#Report_Post_" + PostID).removeClass("text-success");
        $("#Report_Post_" + PostID).addClass("text-danger");
      }
    });
  });

  function ReportComment(CommentID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?CommentID=" + CommentID + "&operation=repComment",
      success: function(result) {
        $("#Report_Comment_" + CommentID).html('<i class="fas fa-headset delreportcomment text-success delreportitem_' + CommentID + '" onClick="DelReportComment(' + CommentID + ')"></i>');
        $("#reportitem_" + CommentID).removeClass("text-danger");
        $("#Report_Comment_" + CommentID).addClass("text-success");
      }
    });
  }

  function DelReportComment(CommentID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?CommentID=" + CommentID + "&operation=delrepComment",
      success: function(result) {
        $("#Report_Comment_" + CommentID).html('<i class="fas fa-bug reportcomment text-danger reportitem_' + CommentID + '" onClick="ReportComment(' + CommentID + ')"></i>');
        $("#delreportitem_" + CommentID).removeClass("text-success");
        $("#Report_Comment_" + CommentID).addClass("text-danger");
      }
    });
  }

  function DeletePost(Operation, MemberID, PostID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?ID=" + MemberID + "&PostID=" + PostID + "&operation=" + Operation,
      success: function() {
        location.reload();
      }
    });
  }

  $(".delConfession").on("click", function() {
    var CnfnID = $(this).attr("cnfnid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxconfessions.php?operation=deleteConfesssion",
      data: {
        "CnfnID": CnfnID
      },
      dataType: "json",
      success: function(result) {
        $("#" + CnfnID).remove();
      }
    });
  });

  function SendFormPass(FormID, Operation, SendURL = "") {
    $("#pass_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#submitpassword").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=" + Operation,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#pass_spinner").html("");
        $("#submitpassword").prop("disabled", false);
        if (result.error) {
          $("#pass-result").addClass("bg-danger text-light p-3 mb-3 w-75 mx-auto text-center rounded-3");
          $("#pass-result").html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
          $("#pass_old").css("border", "");
          $("#pass_new").css("border", "");
          $("#pass_new_again").css("border", "");
          $(result.errorinput1).css("border", "2px solid red");
          $(result.errorinput2).css("border", "2px solid red");
          $(result.errorinput3).css("border", "2px solid red");
        }
        if (result.success) {
          $("form").trigger("reset");
          $("#pass-result").removeClass("bg-danger");
          $("#pass-result").addClass("bg-success text-light p-3 mb-3 w-75 mx-auto text-center rounded-3");
          $("#pass-result").html('<i class="fas fa-check-square"></i> ' + result.success);
          $("#pass_old").css("border", "");
          $("#pass_new").css("border", "");
          $("#pass_new_again").css("border", "");
        }
      }
    });
  }

  function SendFormAccount(FormID, Operation, What) {
    $("#" + What + "_spinner").html(' <i class="fas fa-spinner fa-spin"></i>');
    $(".submitaccount").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=" + Operation,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#" + What + "_spinner").html("");
        $(".submitaccount").prop("disabled", false);
        if (result.error) {
          $("#account_result").addClass("bg-danger text-light p-3 text-center rounded-3");
          $("#account_result").html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
          $(result.errorinput + "_input").css("border", "2px solid red");
        }
        if (result.success) {
          $("form").trigger("reset");
          window.location.href = SITE_URL + "/socialmedia/<?= $translates["settings"] ?>/<?= $translates["account"] ?>";
        }
      }
    });
  }

  $("#faculty_selectbox").on("change", function() {
    var FacultyID = $(this).val();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=openEditDepartment",
      data: {
        "FacultyID": FacultyID
      },
      dataType: "json",
      success: function(result) {
        $("#department_selectbox").html(result.output);
      }
    });
  });

  function SendFormAbout(FormID, Operation, AboutItem) {
    $("#" + AboutItem + "_spinner").html(' <i class="fas fa-spinner fa-spin"></i>');
    $(".submitabout").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    if (FormID == "form_hobbies") {
      var Hobbies = $("#added_hobbies").attr("hobbies");
      Datas += "&hobbies=" + Hobbies;
    }
    console.log(Datas);
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=" + Operation,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#" + AboutItem + "_spinner").html("");
        $(".submitabout").prop("disabled", false);
        if (result.success) {
          $("#" + AboutItem).removeClass("d-none");
          $("#" + AboutItem).addClass("d-inline-table");
          $("#edit_" + AboutItem).removeClass("d-inline-table");
          $("#edit_" + AboutItem).addClass("d-none");
          $("#contents_about_" + AboutItem).html(result.success);
          $("#about_result").removeClass("bg-danger text-light p-3 mb-3 text-center rounded-3");
          $("#about_result").html("");
          if (AboutItem == "faculty" && result.success == '<?= $translates["undefined"] ?>') {
            $("#contents_about_department").html(result.success);
          }
        } else if (result.error) {
          $("html, body").animate({
            scrollTop: $(document).height()
          }, 100);
          $("#about_result").addClass("bg-danger text-light p-3 mb-3 text-center rounded-3");
          $("#about_result").html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
        }
      }
    });
  }

  function SendFormResume(FormID, Operation, ResumeItem) {
    $("#" + ResumeItem + "_spinner").html(' <i class="fas fa-spinner fa-spin"></i>');
    $(".submitresume").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    if (FormID == "form_j_exp") {
      var jobs = $("#added_jobs").attr("jobs");
      Datas += "&jobs=" + jobs;
    }
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=" + Operation,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#" + ResumeItem + "_spinner").html("");
        $(".submitresume").prop("disabled", false);
        if (result.success) {
          $("#" + ResumeItem).removeClass("d-none");
          $("#" + ResumeItem).addClass("d-inline-table");
          $("#edit_" + ResumeItem).removeClass("d-inline-table");
          $("#edit_" + ResumeItem).addClass("d-none");
          $("#contents_resume_" + ResumeItem).html(result.success);
          $("#resume_result").removeClass("bg-danger text-light p-3 mb-3 text-center rounded-3");
          $("#resume_result").html("");
        } else if (result.error) {
          $("html, body").animate({
            scrollTop: $(document).height()
          }, 100);
          $("#resume_result").addClass("bg-danger text-light p-3 mb-3 text-center rounded-3");
          $("#eesume_result").html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
        }
      }
    });
  }

  function RemoveMember(Operation) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=" + Operation,
      success: function(result) {
        window.location.href = "http://localhost/aybu/socialmedia/exit.php";
      }
    });
  }

  //Şifre Sıfırla AJAX
  function ResetPassword(FormID, Operation, Spinner, ResultSpan) {
    $("#" + Spinner).html('<i class="fas fa-spinner fa-spin"></i>');
    $("#forgotpass_btn").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxresetpass.php?operation=" + Operation,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#" + Spinner).html("");
        $("#forgotpass_btn").prop("disabled", false);
        if (result.error) {
          $("#" + ResultSpan).addClass("bg-danger text-light rounded-2 my-3 p-3");
          $("#" + ResultSpan).html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
        } else if (result.success) {
          $("form").trigger("reset");
          $("#" + ResultSpan).addClass("bg-success text-light rounded-2 my-3 p-3");
          $("#" + ResultSpan).html("<i class='fas fa-check-square'></i> " + result.success);
          if (Operation == 'sendmail') {
            setTimeout(function() {
              window.location.href = "http://localhost/aybu/socialmedia/<?= $translates["forgotpassword"] ?>/<?= $translates["secondstep"] ?>/" + result.code;
            }, 2000);
          } else if (Operation == 'resetpass') {
            setTimeout(function() {
              window.location.href = "http://localhost/aybu/socialmedia";
            }, 2000);
          }
        }
      }
    });
  }

  // Messenger AJAX
  var log = $('#messages_container');
  log.animate({
    scrollTop: log.prop('scrollHeight')
  }, 200);

  $(function() {

    $("#form_send_img").change(function(e) {
      var Datas = new FormData(this);
      var FromID = ID;
      if (GroupID == "0") {
        var Operation = "sendimg";
        var ToID = Part;
      } else {
        var Operation = "sendimg_group";
        var ToID = GroupID;
      }
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=" + Operation + "&FromID=" + FromID + "&ToID=" + ToID,
        data: Datas,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(result) {
          if (result.imgMsg) {
            log.animate({
              scrollTop: log.prop('scrollHeight')
            }, 200);
            $("#messages_container").append(result.imgMsg);
            if (result.nonconversation) {
              $("#contactmain").prepend(result.nonconversation);
            }
            if (result.conversationtrue) {
              $("#person_" + result.personID).remove();
              $("#contactmain").prepend(result.conversationtrue);
            }
          }
          if (result.error) {
            alert(result.error);
          }
        }
      });
    });
    $("#groupmembers").on("click", function() {
      $("#allFriends").slideToggle();
    });
    $("#groupmembers").on("keyup", function() {
      var person_info = $(this).val();
      var addedFriends = $(this).attr("alladded");
      $.ajax({
        method: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=searchFriends",
        data: {
          "search": person_info,
          "addedFriends": addedFriends,
        },
        dataType: "json",
        success: function(result) {
          $("#FriendsList").html(result.friends);
        }
      });
    });

    $("#FriendsList").on("click", ".each-friend", function() {
      var FriendID = $(this).attr("id");
      $.ajax({
        method: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=addedFriends",
        data: {
          "FriendID": FriendID
        },
        dataType: "json",
        success: function(result) {
          $("#containermembers").append(result.addedFriends);
          var oldAdded = $("#groupmembers").attr("alladded");
          var newAdded = oldAdded + FriendID + ":";
          $("#groupmembers").attr("alladded", newAdded);
          $("#" + FriendID).remove();
        }
      });
    });

    $("#containermembers").on("click", ".removeaddedfriend", function() {
      var FriendID = $(this).attr("friendid");
      var allAdded = $("#groupmembers").attr("alladded");
      $.ajax({
        method: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=removeaddedFriend",
        data: {
          "FriendID": FriendID,
          "allAdded": allAdded
        },
        dataType: "json",
        success: function(result) {
          $("#groupmembers").attr("alladded", result.alladded);
          $("#friendid_" + FriendID).remove();
          $("#FriendsList").append(result.friends);
        }
      });
    });

    $("#form_createGroup").on("submit", function(e) {
      var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
      var filePath = $("#groupimg").val();
      if (!allowedExtensions.exec(filePath) && filePath != "") {
        $("#resultgroup").removeClass("bg-success");
        $("#resultgroup").addClass("bg-danger text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
        $("#resultgroup").html('<?= $translates["notallowedimg"] ?>');
        location.reload();
      } else {
        e.preventDefault();
        $("#spinneraddgroup").html('<i class="fas fa-spinner fa-spin"></i>');
        $("#addgroup_btn").prop("disabled", true);
        var Datas = new FormData(this);
        var GroupMembers = $("#groupmembers").attr("alladded");
        Datas.append("groupname", $("#groupname").val());
        Datas.append("groupexp", $("#groupexp").val());
        Datas.append("GroupMembers", GroupMembers);
        $.ajax({
          type: "post",
          url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=createGroup",
          data: Datas,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          success: function(result) {
            $("#spinneraddgroup").html("");
            $("#addgroup_btn").prop("disabled", false);
            if (result.error) {
              $("#resultgroup").addClass("bg-danger text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
              $("#resultgroup").html(result.error);
            } else {
              $("form").trigger("reset");
              $("#resultgroup").removeClass("bg-danger");
              $("#resultgroup").addClass("bg-success text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
              $("#resultgroup").html(result.success);
              $("#allFriends").slideToggle();
              $("#containermembers").html("");
              $("#contactmain").prepend(result.groupcontact);
            }
          }
        });
      }
    });

    $.ajaxloadmessages = function() {
      var partID = Part;
      var lastMessage = $("#messages_container li:last").attr("lastid");
      var Datas = {
        "lastid": lastMessage,
        "personID": partID,
        "GroupID": GroupID
      };
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=showmessages",
        data: Datas,
        dataType: 'json',
        success: function(result) {
          if (result.seen) {
            $(".seentic").html(result.seen);
          }
          $("#messages_container").append(result.message);
          if (result.message) {
            log.animate({
              scrollTop: log.prop('scrollHeight')
            }, 200);
            if (result.nonconversation) {
              $("#contactmain").prepend(result.nonconversation);
            }
            if (result.conversationtrue) {
              $("#person_" + result.personID).remove();
              $("#contactmain").prepend(result.conversationtrue);
            }
          }
        }
      });
    }

    $.ajaxdeleteControl = function() {
      var partID = Part;
      var lastMessage = $("#messages_container li:last").attr("lastid");
      var Datas = {
        "lastid": lastMessage,
        "personID": partID,
        "GroupID": GroupID
      };
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=deleteControl",
        data: Datas,
        dataType: 'json',
        success: function(result) {
          var deletedMessages = result.MessageID;
          if (result.MessageID) {
            var msgNum = result.len;
            let msgArr = deletedMessages.split(" ");
            for (var i = 0; i < msgNum; i++) {
              $("#each_message_" + msgArr[i]).css("opacity", "0");
              $("#each_message_" + msgArr[i]).remove();
              if (!result.nomsg) {
                $("#content_" + result.personID).html(result.lastcontent);
              }
              $("#chatpersontime_" + result.personID).html(result.messagetime);
              if (result.nomsg) {
                if (!GroupID) {
                  $("#person_" + partID).remove();
                }
              }
            }
          }
        }
      });
    }

    $.getMessage = function() {
      var partID = Part;
      var Datas = {
        "partID": partID,
        "GroupID": GroupID
      };
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=getmessage",
        data: Datas,
        dataType: 'json',
        success: function(result) {
          if (result.newMessages) {
            if (Page != '<?= $translates["messages"] ?>') {
              $(".toast-container").append(result.toast);
            }
            setTimeout(() => {
              $(".toast").css({
                "opacity": 0,
                "visibility": "hidden"
              });
            }, 5000);

            if (result.nonconversation) {
              $("#contactmain").prepend(result.nonconversation);
            }
            if (result.conversationtrue) {
              $("#person_" + result.personID).remove();
              $("#contactmain").prepend(result.conversationtrue);
            }
            if (result.deleted) {
              $("#content_" + result.personID).html(result.lastcontent);
              $("#content_" + result.personID).css("opacity", "0.5");
              $("#chatpersontime_" + result.personID).html(result.messagetime);
              if (result.nomsg) {
                $("#person_" + result.personID).remove();
              }
              if (result.opacity) {
                $(".content").css("opacity", "0.7");
              }
            }
          }
        }
      });
    }

    $.setStatus = function() {
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=setStatus",
        success: function(result) {
          var nothing = "nothing";
        }
      });
    }

    $.getStatus = function() {
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=getStatus",
        success: function(result) {
          var result = result.split(":::");
          $(".offline").css("color", "rgb(204, 1, 1)");
          for (let i = 0; i < result.length; i++) {
            $("#chatperson_" + result[i]).css("color", "green");
            $("#chatfriend_" + result[i]).css("color", "green");
          }
        }
      });
    }

    $.setNoti = function() {
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxnotifications.php?operation=setNoti",
        dataType: 'json',
        success: function(result) {
          if (result.what) {
            $(".nonoti").remove();
            $("#noticon_mb").addClass("navanimate");
            $("#notiDropdown").addClass("navanimate");
            $(".notificationIcon").addClass("iconanimate");
            $(".friend_requests_noti").prepend(result.data);
          } else {
            $("#noticon_mb").removeClass("navanimate");
            $("#notiDropdown").removeClass("navanimate");
            $(".notificationIcon").removeClass("iconanimate");
          }
          $("#each_mb_noti_" + result.deletednotiID).remove();
          $("#each_noti_" + result.deletednotiID).remove();
          $("#noti_count").html(result.newCountNoti);
        }
      });
    }

    $("#searchMessage").on("keyup", function() {
      var searchedKey = $(this).val();
      if (searchedKey != "") {
        var Datas = {
          "searchedKey": searchedKey,
          "personID": Part,
          "GroupID": GroupID
        };
        $.ajax({
          type: "post",
          url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=searchMessage",
          data: Datas,
          dataType: 'json',
          success: function(result) {
            var sumOfHeights = 0;
            for (var i = 0; i < result.count; i++) {
              sumOfHeights += $("#each_message_" + result.beforeIDs[i]).height();
            }
            scrollVal = sumOfHeights;
            $('#messages_container').animate({
              scrollTop: scrollVal
            }, 10);
            $(".list-group-item").removeClass("bg-light");
            $(".list-group-item").addClass("bg-transparent");
            $("#each_message_" + result.messageID).removeClass("bg-transparent");
            $("#each_message_" + result.messageID).addClass("bg-light");
          }
        });
      }
    });
  });

  <?php if ($page == $translates["messages"]) { ?>
    setInterval('$.ajaxloadmessages()', 1000);
    setInterval('$.ajaxdeleteControl()', 1000);
    setInterval('$.getStatus()', 1000);
  <?php } ?>
  setInterval('$.getMessage()', 1000);
  setInterval('$.setStatus()', 1000);
  setInterval('$.setNoti()', 2000);

  function SendMessage(Operation, FromID, ToID) {
    var Datas = $("form#form_send_message").serialize();
    $("#spinnersendmessage").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#sendMessageBtn").prop("disabled", true);
    $("#papericon").css("display", "none");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=" + Operation + "&FromID=" + FromID + "&ToID=" + ToID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("form").trigger("reset");
        $("#spinnersendmessage").html("");
        $("#papericon").css("display", "inline-block");
        $("#sendMessageBtn").prop("disabled", false);
        if (result.message) {
          $("#sendMessageBtn").prop("disabled", true);
          $("#sendMessageBtn").html('<i class="fas fa-hourglass-half fa-spin" style="margin:0px 2px;"></i>');
          if (result.nonconversation) {
            $("#contactmain").prepend(result.nonconversation);
          }
          if (result.conversationtrue) {
            $("#person_" + result.personID).remove();
            $("#contactmain").prepend(result.conversationtrue);
          }
          $("#messages_container").append(result.lastsentmsg);
          setTimeout(function() {
            $("#sendMessageBtn").prop("disabled", false);
            $("#sendMessageBtn").html('<span class="spinner" id="spinnersendmessage"></span><i class="far fa-paper-plane" id="papericon"></i>');
          }, 1000);
        }
        log.animate({
          scrollTop: log.prop('scrollHeight')
        }, 200);
      }
    });
  }

  function DeleteMessage(Operation, MessageID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=" + Operation,
      data: {
        "MessageID": MessageID
      },
      dataType: "json",
      success: function(result) {
        if (result.nomessage) {
          $("#person_" + result.personID).remove();
        }
        $("#each_message_" + MessageID).css("opacity", "0");
        setTimeout(function() {
          $("#each_message_" + MessageID).css("display", "none");
          $("#chatpersontime_" + result.personID).html(result.msgtime);
        }, 200);
        $("#content_" + result.personID).html(result.personabs);
      }
    });
  }

  // Bildirimler
  $("#notifications_mobile").on("click", function() {
    var stateBox = $("#noti_box_mb").attr("state");
    if (stateBox == "closed") {
      $("#noti_box_mb").slideDown('slow');
      $("#noti_box_mb").attr("state", "open");
    } else {
      $("#noti_box_mb").slideUp('slow');
      $("#noti_box_mb").attr("state", "closed");
    }

  });

  function deleteNotis() {
    $(".notificationIcon").removeClass("iconanimate");
    $("#noticon_mb").removeClass("navanimate");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnotifications.php?operation=deleteNotifications",
      dataType: "json",
      success: function(result) {}
    });
  }

  //Kulüp İşlemleri

  $("#search_clubs").on("keyup", function() {
    var club_info = $(this).val();
    $.ajax({
      method: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=searchClubs",
      data: {
        "search": club_info
      },
      dataType: "json",
      success: function(result) {
        $("#all_clubs").html(result.clubs);
      }
    });
  });

  $("#form_addClub").on("submit", function(e) {
    var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    var filePath = $("#clubimg").val();
    if (!allowedExtensions.exec(filePath) && filePath != "") {
      alert('Invalid file type');
      location.reload();
    } else {
      e.preventDefault();
      $("#spinneraddclub").html('<i class="fas fa-spinner fa-spin"></i>');
      $("#addclub_btn").prop("disabled", true);
      var Datas = new FormData(this);
      Datas.append("clubname", $("#clubname").val());
      Datas.append("clubscope", $("#clubscope").val());
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxclub.php?operation=addclub",
        data: Datas,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(result) {
          $("#spinneraddclub").html("");
          $("#addclub_btn").prop("disabled", false);
          if (result.error) {
            $("#result").addClass("bg-danger text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
            $("#result").html(result.error);
          } else {
            $("#result").removeClass("bg-danger");
            $("#result").addClass("bg-success text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
            $("#result").html(result.success);
          }
        }
      });
    }
  });

  $("#container").on("click", "#joinclub", function() {
    $("#spinnerjoinclub").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#joinclub").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=sendJoin",
      data: {
        "ClubID": Part
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnerjoinclub").html("");
        $("#joinclub").prop("disabled", false);
        if (result.success) {
          $("#joinclub").attr("id", "cancelreq");
          $("#cancelreq").html('<?= $translates["sentjoinreq"] ?> <span class="spinner" id="spinnercancelreq">');
        }
      }
    });
  });

  $("#container").on("click", "#cancelreq", function() {
    $("#spinnercancelreq").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#cancelreq").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=cancelReq",
      data: {
        "ClubID": Part
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnercancelreq").html("");
        $("#cancelreq").prop("disabled", false);
        if (result.success) {
          $("#cancelreq").attr("id", "joinclub");
          $("#joinclub").html('<?= $translates["sendjoinreq"] ?> <span class="spinner" id="spinnerjoinclub">');
        }
      }
    });
  });

  $("#container").on("click", "#spamclub", function() {
    $("#spinnerspamclub").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#spamclub").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=spamClub",
      data: {
        "ClubID": Part
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnerspamclub").html("");
        $("#spamclub").prop("disabled", false);
        if (result.success) {
          $("#spamclub").attr("id", "cancelspam");
          $("#cancelspam").html('<?= $translates["spammedclub"] ?> <span class="spinner" id="spinnercancelspam">');
        }
      }
    });
  });

  $("#container").on("click", "#cancelspam", function() {
    $("#spinnercancelspam").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#cancelspam").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=cancelSpam",
      data: {
        "ClubID": Part
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnercancelspam").html("");
        $("#cancelspam").prop("disabled", false);
        if (result.success) {
          $("#cancelspam").attr("id", "spamclub");
          $("#spamclub").html('<?= $translates["spamclub"] ?> <span class="spinner" id="spinnerspamclub">');
        }
      }
    });
  });

  $("#leaveclub").on("click", function() {

    $("#spinnerleaveclub").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#leaveclub").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=leaveClub",
      data: {
        "ClubID": Part
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnerleaveclub").html("");
        $("#leaveclub").prop("disabled", false);
        if (result.success) {
          location.reload();
        }
      }
    });
  });

  //Kulüp Events
  function changeShdwClub(changeTo) {
    $("#posts_container-tab").removeClass("shadow");
    $("#events-tab").removeClass("shadow");
    $("#members-tab").removeClass("shadow");
    $("#" + changeTo).addClass("shadow");
  }

  function changeShdwSettings(changeTo) {
    $("#account-tab").removeClass("shadow");
    $("#about-tab").removeClass("shadow");
    $("#resume-tab").removeClass("shadow");
    $("#password-tab").removeClass("shadow");
    $("#" + changeTo).addClass("shadow");
  }

  $("#form_event").on("submit", function(e) {
    e.preventDefault();
    $("#spinnerevent").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#submitevent").prop("disabled", true);
    var Datas = $("#form_event").serialize();
    Datas = Datas + "&ClubID=" + Part;
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=addEvent",
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#spinnerevent").html('');
        $("#submitevent").prop("disabled", false);
        if (result.error) {
          $("#result").addClass("bg-danger mt-2 mb-3 m-md-0 text-light p-2 w-100 mb-0 mx-auto text-center rounded-3");
          $("#result").html(result.error);
        } else {
          $("#result").removeClass("bg-danger");
          $("#result").addClass("bg-success mt-2 mb-3 m-md-0 text-light p-2 w-100 mb-0 mx-auto text-center rounded-3");
          $("#result").html(result.success);
          $("form").trigger("reset");
          $("#allEvents").prepend(result.newEvent);
        }
      }
    });
  });

  $("#events").on("click", ".joinevent", function() {
    var EventID = $(this).attr("eventid");
    $("#spinnerjoin_" + EventID).html('<i class="fas fa-spinner fa-spin"></i>');
    $("#joinevent_" + EventID).prop("disabled", true);
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=joinEvent",
      data: {
        "ClubID": Part,
        "EventID": EventID
      },
      dataType: "json",
      success: function(result) {
        $("#spinnerjoin_" + EventID).html('');
        $("#joinevent_" + EventID).prop("disabled", false);
        $("#joinevent_" + EventID).removeClass("btn-success");
        $("#joinevent_" + EventID).addClass("btn-primary");
        $("#joinevent_" + EventID).removeClass("joinevent");
        $("#joinevent_" + EventID).addClass("canceljoin");
        $("#joinevent_" + EventID).html("<?= $translates["canceljoin"] ?> <span class='spinner' id='spinnercanceljoin'></span>");
        $("#eventparticipant_" + EventID).html('<?= $translates["eventparticipant"] ?>: ' + result.participantnumber);
        $("#joinevent_" + EventID).attr("id", "canceljoin_" + EventID);
      }
    });
  });

  $("#events").on("click", ".canceljoin", function() {
    var EventID = $(this).attr("eventid");
    $("#spinnercanceljoin_" + EventID).html('<i class="fas fa-spinner fa-spin"></i>');
    $("#canceljoin_" + EventID).prop("disabled", true);
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=cancelJoin",
      data: {
        "ClubID": Part,
        "EventID": EventID
      },
      dataType: "json",
      success: function(result) {
        $("#spinnercanceljoin_" + EventID).html('');
        $("#canceljoin_" + EventID).prop("disabled", false);
        $("#canceljoin_" + EventID).removeClass("btn-primary");
        $("#canceljoin_" + EventID).addClass("btn-success");
        $("#canceljoin_" + EventID).removeClass("canceljoin");
        $("#canceljoin_" + EventID).addClass("joinevent");
        $("#canceljoin_" + EventID).html('<?= $translates["join"] ?> <span class="spinner" id="spinnerjoin"></span>');
        $("#eventparticipant_" + EventID).html('<?= $translates["eventparticipant"] ?>: ' + result.participantnumber);
        $("#canceljoin_" + EventID).attr("id", "joinevent_" + EventID);
      }
    });
  });

  $("#events").on("click", ".deleteEvent", function() {
    var EventID = $(this).attr("eventid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=deleteEvent",
      data: {
        "ClubID": Part,
        "EventID": EventID
      },
      dataType: "json",
      success: function(result) {
        $(".event_" + EventID).remove();
      }
    });
  });

  $(".accept-request").on("click", function() {
    var MembershipID = $(this).attr("membershipid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=acceptRequest",
      data: {
        "ClubID": Part,
        "MembershipID": MembershipID
      },
      dataType: "json",
      success: function(result) {
        $("#request_" + MembershipID).remove();
        $("#containermembers").append(result.carouselitem);
        $("#number_member").html(result.newNumber);
        if (result.anyleft) {
          $("#membership_requests").remove();
        }
      }
    });
  });
  $(".refuse-request").on("click", function() {
    var MembershipID = $(this).attr("membershipid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=refuseRequest",
      data: {
        "ClubID": Part,
        "MembershipID": MembershipID
      },
      dataType: "json",
      success: function(result) {
        $("#request_" + MembershipID).remove();
        if (result.anyleft) {
          $("#membership_requests").remove();
        }
      }
    });
  });

  $("#members").on("click", ".removeMember", function() {
    var ClubMemberID = $(this).attr("clubmemberid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=removeMember",
      data: {
        "ClubID": Part,
        "ClubMemberID": ClubMemberID
      },
      dataType: "json",
      success: function(result) {
        $("#clubMember_" + ClubMemberID).remove();
        $("#number_member").html(result.newNumber);
      }
    });
  });
  $("#members").on("click", ".promoteMember", function() {
    var ClubMemberID = $(this).attr("clubmemberid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=promoteMember",
      data: {
        "ClubID": Part,
        "ClubMemberID": ClubMemberID
      },
      dataType: "json",
      success: function(result) {
        $("#clubMember_" + ClubMemberID).remove();
        $("#containermanagement").append(result.member);
      }
    });
  });
  $("#members").on("click", ".deductMember", function() {
    var ClubMemberID = $(this).attr("clubmemberid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxclub.php?operation=deductMember",
      data: {
        "ClubID": Part,
        "ClubMemberID": ClubMemberID
      },
      dataType: "json",
      success: function(result) {
        $("#clubMember_" + ClubMemberID).remove();
        $("#containermembers").append(result.member);
      }
    });
  });

  //EVENTS
  $("#categorize_items").on("click", function() {
    var selected_unis = $("#selected_unis").attr("selected_unis");
    var least_price = $("#least_price").val();
    if (!least_price) {
      least_price = "0";
    }
    var most_price = $("#most_price").val();
    if (!most_price) {
      most_price = "?";
    }

    if (!pageNum) {
      pageNum = 1;
    }
    if (category != "0") {
      category = "&category=" + category;
    } else {
      category = "";
    }
    if (uni != "0") {
      uni = "&uni=" + uni;
    } else {
      uni = "";
    }
    if (order != "0") {
      order = "&order=" + order;
    } else {
      order = "";
    }

    window.location.href = SITE_URL + "/socialmedia/<?= $translates["events"] ?>?pageNum=" + pageNum + category + uni + "&price=" + least_price + "-" + most_price + order;
  });

  $("#search_school").on("keyup", function() {
    var search_school = $(this).val();
    $.ajax({
      method: "post",
      url: SITE_URL + "/socialmedia/ajaxevents.php?operation=searchSchool",
      data: {
        "search": search_school,
        "uni": uni
      },
      dataType: "json",
      success: function(result) {
        $("#all_schools").html(result.schools);
      }
    });
  });

  $("#all_schools").on("change", ".selectuni", function() {
    var uniID = $(this).attr("id");
    var isselected = $(this).is(":checked");
    if (!pageNum) {
      pageNum = 1;
    }
    if (category != "0") {
      category = "&category=" + category;
    } else {
      category = "";
    }
    if (price != "0") {
      price = "&price=" + price;
    } else {
      price = "";
    }
    if (order != "0") {
      order = "&order=" + order;
    } else {
      order = "";
    }
    if (isselected) {
      if (uni != "0") {
        uniID = uni + "-" + uniID;
      }
      window.location.href = SITE_URL + "/socialmedia/<?= $translates["events"] ?>?pageNum=" + pageNum + category + "&uni=" + uniID + price + order;
    } else {
      if (uni.match(/-/)) {
        $.ajax({
          method: "post",
          url: SITE_URL + "/socialmedia/ajaxevents.php?operation=eject",
          data: {
            "uni": uni,
            "uniID": uniID
          },
          dataType: "json",
          success: function(result) {
            window.location.href = SITE_URL + "/socialmedia/<?= $translates["events"] ?>?pageNum=" + pageNum + category + "&uni=" + result.uni + price + order;
          }
        });
      } else {
        window.location.href = SITE_URL + "/socialmedia/<?= $translates["events"] ?>?pageNum=" + pageNum + category + price + order;
      }
    }
  });

  $("#explanation").on("keyup", function() {
    var char_length = $(this).val().length;
    var max_length = 1000;
    $("#char_left").html(max_length - char_length);
  });

  $("#continue_btn").on("click", function() { // DEVAM ET BUTONU
    // BUTONU KÜÇÜLT
    $(this).removeClass("w-100");
    $(this).addClass("w-70");
    // GERİ BUTONUNU AKTİFLEŞTİR
    $("#back_btn").removeClass("d-none");
    $("#back_btn").addClass("d-block");
    // Basılmadan Önceki SEC
    var currentSec = $("#current_sec").attr("currentsec");
    if (currentSec == "first_sec") {
      // 1.SECI KAPAT
      $("#first_sec").removeClass("d-block");
      $("#first_sec").addClass("d-none");
      // 2.SECI AÇ
      $("#second_sec").removeClass("d-none");
      $("#second_sec").addClass("d-block");
      // CURRENTI DEĞİŞTİR
      $("#current_sec").attr("currentsec", "second_sec");
    }
    if (currentSec == "second_sec") {
      // 2.SECI KAPAT
      $("#second_sec").removeClass("d-block");
      $("#second_sec").addClass("d-none");
      // 3.SECI AÇ
      $("#third_sec").removeClass("d-none");
      $("#third_sec").addClass("d-block");
      // CURRENTI DEĞİŞTİR
      $("#current_sec").attr("currentsec", "third_sec");
      // İLERİ BUTONUNU KALDIR
      $(this).removeClass("d-block");
      $(this).addClass("d-none");
      // OLUŞTUR BUTONUNU GÖSTER
      $("#createEvent_btn").removeClass("d-none");
      $("#createEvent_btn").addClass("d-block");
    }
  });

  $("#back_btn").on("click", function() {
    // Basılmadan Önceki SEC
    var currentSec = $("#current_sec").attr("currentsec");
    if (currentSec == "second_sec") {
      // 1.SECI AÇ
      $("#first_sec").removeClass("d-none");
      $("#first_sec").addClass("d-block");
      // 2.SECI KAPAT
      $("#second_sec").removeClass("d-block");
      $("#second_sec").addClass("d-none");
      // BUTONU BÜYÜT
      $("#continue_btn").removeClass("w-70");
      $("#continue_btn").addClass("w-100");
      // GERİ BUTONUNU KAPAT
      $(this).removeClass("d-block");
      $(this).addClass("d-none");
      // CURRENTI DEĞİŞTİR
      $("#current_sec").attr("currentsec", "first_sec");
    }
    if (currentSec == "third_sec") {
      // 2.SECI AÇ
      $("#second_sec").removeClass("d-none");
      $("#second_sec").addClass("d-block");
      // 3.SECI KAPAT
      $("#third_sec").removeClass("d-block");
      $("#third_sec").addClass("d-none");
      // OLUŞTUR BUTONUNU KAPAT
      $("#createEvent_btn").removeClass("d-block");
      $("#createEvent_btn").addClass("d-none");
      // DEVAM ET BUTONUNU AÇ
      $("#continue_btn").removeClass("d-none");
      $("#continue_btn").addClass("d-block");
      // CURRENTI DEĞİŞTİR
      $("#current_sec").attr("currentsec", "second_sec");
    }
  });

  $("#noCity").on("click", function() {
    $("#cityofEvent").slideToggle();
    $("#placeofEvent").slideToggle();
  });
  $("#free").on("click", function() {
    if ($(this).is(":checked")) {
      $("#pricing").val("");
      $("#pricing").attr("disabled", true);
    } else {
      $("#pricing").attr("disabled", false);
    }

  });

  $("#form_createEvent").on("submit", function(e) {
    e.preventDefault();
    $("#spinnercreateEvent").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#createEvent_btn").prop("disabled", true);
    var Operation = $("#createEvent_btn").attr("operation");
    var Datas = new FormData(this);
    Datas.append("eventHeader", $("#eventHeader").val());
    Datas.append("eventCategory", $("#eventCategory").val());
    Datas.append("noCity", $("#noCity").is(":checked"));
    Datas.append("eventCity", $("#eventCity").val());
    Datas.append("eventPlace", $("#eventPlace").val());
    Datas.append("explanation", $("#explanation").val());
    Datas.append("eventSchool", $("#eventSchool").val());
    Datas.append("eventDate", $("#eventDate").val());
    Datas.append("emailAddress", $("#emailAddress").val());
    Datas.append("phoneNum", $("#phoneNum").val());
    Datas.append("pricing", $("#pricing").val());
    Datas.append("free", $("#free").is(":checked"));
    Datas.append("eventID", $("#createEvent_btn").attr("eventid"));

    var imagePath = $("#eventImg").val();
    var imageallowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;

    if (imagePath && !imageallowedExtensions.exec(imagePath)) {
      $("#result").removeClass("d-none");
      $("#result").addClass("d-block");
      $("#result").html('<?= $translates["notallowedimg"] ?>');
      $("#spinnershare").html("");
      $("#submitpost").prop("disabled", false);
    } else {
      $.ajax({
        type: "post",
        url: SITE_URL + "/socialmedia/ajaxevents.php?operation=" + Operation,
        data: Datas,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(result) {
          $("#spinnercreateEvent").html("");
          $("#createEvent_btn").prop("disabled", false);
          if (result.error) {
            $("#footer_result").addClass("p-3");
            $("#result").addClass("bg-danger text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
            $("#result").html(result.error);
          } else {
            $("#result").removeClass("bg-danger");
            $("#footer_result").addClass("p-3");
            $("#result").addClass("bg-success text-light p-3 w-100 mb-0 mx-auto text-center rounded-3");
            $("#result").html(result.success);
            if (Operation == 'editEvent') {
              setTimeout(function() {
                window.location.href = SITE_URL + "/socialmedia/<?= $translates["events"] ?>/" + result.newlink;
              }, 1000);
            }
          }
        }
      });
    }
  });

  $("#event_buttons").on("click", "#joinEvent", function() {
    $("#spinnerJoin").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#joinEvent").prop("disabled", true);
    var EventID = $(this).attr("eventid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxevents.php?operation=joinEvent",
      data: {
        "EventID": EventID
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnerJoin").html("");
        $("#joinEvent").prop("disabled", false);
        $("#joinEvent").removeClass("btn-secondary");
        $("#joinEvent").addClass("btn-success");
        $("#joinEvent").html('<?= $translates["joinedtoevent"] ?> <span class="spinner" id="spinnercancelJoin">');
        $("#joinEvent").attr("id", "cancelJoin");
        $("#participantNum").html(result.newNumber);
      }
    });
  });

  $("#event_buttons").on("click", "#cancelJoin", function() {
    $("#spinnercancelJoin").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#cancelJoin").prop("disabled", true);
    var EventID = $(this).attr("eventid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxevents.php?operation=cancelJoin",
      data: {
        "EventID": EventID
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnercancelJoin").html("");
        $("#cancelJoin").prop("disabled", false);
        $("#cancelJoin").removeClass("btn-success");
        $("#cancelJoin").addClass("btn-secondary");
        $("#cancelJoin").html('<?= $translates["jointoevent"] ?> <span class="spinner" id="spinnerJoin">');
        $("#cancelJoin").attr("id", "joinEvent");
        $("#participantNum").html(result.newNumber);
      }
    });
  });

  $("#getPremium").on("click", function() {
    $("#spinnerProEvent").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#getPremium").prop("disabled", true);
    var EventID = $(this).attr("eventid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxevents.php?operation=getPremium",
      data: {
        "EventID": EventID
      },
      dataType: "JSON",
      success: function(result) {
        $("#spinnerProEvent").html("");
        $("#getPremium").prop("disabled", false);
        $("#getPremium").html('Premium Etkinlik <i class="fas fa-check"></i>');
        $(this).attr("id", "");
      }
    });

  });
  //Biografi
  $("#submitBio").on("click", function() {
    $("#spinnerbio").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#submitBio").prop("disabled", true);
    var Datas = $("form#form_bio").serialize();
    console.log(Datas);
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxsettings.php?operation=editBio",
      data: Datas,
      dataType: "JSON",
      success: function(result) {
        $("#spinnerbio").html("");
        $("#submitBio").prop("disabled", false);
        location.reload();
      }
    });
  });

  //KURSLAR
  $("#searchCourse").on("keyup", function() {
    var searchedKey = $(this).val();
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxcourses.php?operation=searchCourse",
      data: {
        "searchedKey": searchedKey
      },
      dataType: "JSON",
      success: function(result) {
        $("#allCourses").html(result.courses);
      }
    });
  });

  $("#allCourses").on("change", ".selectCourse", function() {
    var CourseID = $(this).attr("courseid");
    var State = $(this).is(":checked");
    if (State) {
      State = "add";
    } else {
      State = "remove";
    }
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxcourses.php?operation=submitCourse",
      data: {
        "CourseID": CourseID,
        "State": State
      },
      dataType: "JSON",
      success: function(result) {}
    });
  });

  $("#submitCourses").on("click", function() {
    location.reload();
  });

  // GRUP
  $(".editExp").on("click", function() {
    $("#expInput").removeClass("d-none");
    $("#expBtn").removeClass("d-none");
    $("#groupExp").addClass("d-none");
    $(".editExp").addClass("d-none");
  });
  $(".editgroupName").on("click", function() {
    $("#groupNameInput").removeClass("d-none");
    $("#nameBtn").removeClass("d-none");
    $("#groupName").addClass("d-none");
    $(".editgroupName").addClass("d-none");
  });

  $("#expBtn").on("click", function() {
    $("#spinnerExp").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#expBtn").prop("disabled", true);
    var newDesc = $("#expInput").val();
    var groupID = $("#expBtn").attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=changeDesc",
      dataType: "JSON",
      data: {
        'newDesc': newDesc,
        'groupID': groupID
      },
      success: function(result) {
        $("#spinnerExp").html('');
        $("#expBtn").prop("disabled", false);
        $("#expInput").addClass("d-none");
        $("#expBtn").addClass("d-none");
        $("#groupExp").removeClass("d-none");
        $(".editExp").removeClass("d-none");
        if (newDesc == "") {
          newDesc = '<?= $translates["nogroupexp"] ?>'
        }
        $("#groupExp").html(newDesc);
      }
    });
  });

  $("#nameBtn").on("click", function() {
    $("#spinnerName").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#nameBtn").prop("disabled", true);
    var newName = $("#groupNameInput").val();
    var groupID = $("#nameBtn").attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=changeName",
      dataType: "JSON",
      data: {
        'newName': newName,
        'groupID': groupID
      },
      success: function(result) {
        $("#spinnerName").html('');
        $("#nameBtn").prop("disabled", false);
        $("#groupNameInput").addClass("d-none");
        $("#nameBtn").addClass("d-none");
        $("#groupName").removeClass("d-none");
        $(".editgroupName").removeClass("d-none");
        if (newName == "") {
          newName = '<?= $translates["anonymousgrp"] ?>'
        }
        $("#chatgroupname").html(newName);
        $("#chatbox_name_" + groupID).html('<i class="fas fa-users" style="font-size: 17px;"></i> ' + newName);
        $("#groupName").html(newName);
      }
    });
  });


  $("#groupMemberName").on("keyup", function() {
    var searchedKey = $(this).val();
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=searchMember",
      data: {
        "searchedKey": searchedKey,
        "groupID": groupID
      },
      dataType: "JSON",
      success: function(result) {
        $("#groupMembers").html(result.members);
      }
    });
  });

  $("#allMembersName").on("keyup", function() {
    var searchedKey = $(this).val();
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=searchallMembers",
      data: {
        "searchedKey": searchedKey,
        "groupID": groupID
      },
      dataType: "JSON",
      success: function(result) {
        $("#allMembers").html(result.members);
      }
    });
  });

  $("#groupMembers").on("click", ".removeMember", function() {
    var MemberID = $(this).attr("memberid");
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=removeMember",
      dataType: "JSON",
      data: {
        'MemberID': MemberID,
        'groupID': groupID
      },
      success: function(result) {
        $("#groupMember_" + MemberID).remove();
        $("#groupMemberNum").html(result.newNum);
      }
    });
  });

  $("#groupMembers").on("click", ".demoteMember", function() {
    var MemberID = $(this).attr("memberid");
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=demoteMember",
      dataType: "JSON",
      data: {
        'MemberID': MemberID,
        'groupID': groupID
      },
      success: function(result) {
        $("#division_" + MemberID).html('<i class="fas fa-angle-double-up px-1"></i>');
        $("#division_" + MemberID).removeClass('btn-outline-warning');
        $("#division_" + MemberID).addClass('promoteMember');
        $("#division_" + MemberID).addClass('btn-outline-success');
        $("#division_" + MemberID).removeClass('demoteMember');
        $("#admin_" + MemberID).remove();
      }
    });
  });

  $("#groupMembers").on("click", ".promoteMember", function() {
    var MemberID = $(this).attr("memberid");
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=promoteMember",
      dataType: "JSON",
      data: {
        'MemberID': MemberID,
        'groupID': groupID
      },
      success: function(result) {
        $("#division_" + MemberID).html('<i class="fas fa-angle-double-down px-1"></i>');
        $("#division_" + MemberID).removeClass('btn-outline-success');
        $("#division_" + MemberID).addClass('btn-outline-warning');
        $("#division_" + MemberID).addClass('demoteMember');
        $("#division_" + MemberID).removeClass('promoteMember');
        $("#memberOperations_" + MemberID).prepend('<span class="p-1 rounded-1" style="color:green;border:1px solid green;font-size:12px" id="admin_' + MemberID + '"><?= $translates["gradmin"] ?></span>');
      }
    });
  });

  $("#allMembers").on("click", ".addMemberIcon", function() {
    var MemberID = $(this).attr("memberid");
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=addMember",
      dataType: "JSON",
      data: {
        'MemberID': MemberID,
        'groupID': groupID
      },
      success: function(result) {
        $("#icon_" + MemberID).removeClass('fa-plus');
        $("#icon_" + MemberID).addClass('fa-check');
        $("#operation_" + MemberID).removeClass("addMemberIcon");
        $("#operation_" + MemberID).addClass("removeMemberIcon");
      }
    });
  });

  $("#allMembers").on("click", ".removeMemberIcon", function() {
    var MemberID = $(this).attr("memberid");
    var groupID = $(this).attr("groupid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=removeMember",
      dataType: "JSON",
      data: {
        'MemberID': MemberID,
        'groupID': groupID
      },
      success: function(result) {
        $("#icon_" + MemberID).removeClass('fa-check');
        $("#icon_" + MemberID).addClass('fa-plus');
        $("#operation_" + MemberID).removeClass("removeMemberIcon");
        $("#operation_" + MemberID).addClass("addMemberIcon");
      }
    });
  });

  $("#form_group_img").on("change", function() {
    var Datas = new FormData(this);
    Datas.append("groupID", GroupID);
    var imagePath = $("#upload_groupimg").val();
    if (imagePath) {
      var imageallowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
      if (imagePath && !imageallowedExtensions.exec(imagePath)) {
        $("#resultImg").removeClass("d-none");
        $("#resultImg").addClass("d-block");
        $("#resultImg").html('<i class="fas fa-exclamation"></i> <?= $translates["notallowedimg"] ?>');
      } else {
        $.ajax({
          type: "post",
          url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=changeImg",
          data: Datas,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          success: function(result) {
            $("#chatpersonimg").attr("src", result.imgsrc);
            $("#groupImage_" + GroupID).attr("src", result.imgsrc);
            $("#changeGroupImg").attr("src", result.imgsrc);
          }
        });
      }
    }
  });

  $("#leaveGroup").on("click", function() {
    $("#spinnerleaveGroup").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#leaveGroup").prop("disabled", true);
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxmessages.php?operation=leaveGroup",
      data: {
        "groupID": GroupID,
      },
      dataType: "json",
      success: function(result) {
        $("#spinnerleaveGroup").html('');
        $("#leaveGroup").prop("disabled", false);
        window.location.href = SITE_URL + "/socialmedia/<?= $translates["messages"] ?>";
      }
    });
  });

  $(".courseFilter").on("keyup", function() {
    var CourseName = $("#courseName").val();
    var CourseCode = $("#courseCode").val();
    var CourseClass = $("#CourseClass").val();
    var Datas = {
      "CourseName": CourseName,
      "CourseCode": CourseCode,
      "CourseClass": CourseClass
    };
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxcourses.php?operation=filterCourse",
      dataType: "JSON",
      data: Datas,
      success: function(result) {
        $("#courseContainer").html(result.course);
        if (!result.course) {
          $("#courseContainer").html("<h4 class='text-dark'><?= $translates["noresult"] ?></h4>");
        }
      }
    });
  });
  $(".courseFilter").on("change", function() {
    var CourseName = $("#courseName").val();
    var CourseCode = $("#courseCode").val();
    var CourseClass = $("#CourseClass").val();
    var Datas = {
      "CourseName": CourseName,
      "CourseCode": CourseCode,
      "CourseClass": CourseClass
    };
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxcourses.php?operation=filterCourse",
      dataType: "JSON",
      data: Datas,
      success: function(result) {
        $("#courseContainer").html(result.course);
        if (!result.course) {
          $("#courseContainer").html("<h4 class='text-dark'><?= $translates["noresult"] ?></h4>");
        }
      }
    });
  });

  $("#coursePage").on("click", ".courseattendance", function() {
    var Operation = $(this).attr("id");
    var CourseID = $(this).attr("courseid");
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxcourses.php?operation=" + Operation,
      dataType: "JSON",
      data: {
        "CourseID": CourseID
      },
      success: function(result) {
        if (Operation == "enrollCourse") {
          $("#enrollCourse").removeClass("btn-post");
          $("#enrollCourse").addClass("btn-secondary");
          $("#enrollCourse").html('<?= $translates["hascourse"] ?> <i class="fas fa-check"></i');
          $("#enrollCourse").attr("id", "quitCourse");
          $("#courseAttandance").html(result.attandance);
        } else {
          $("#quitCourse").removeClass("btn-secondary");
          $("#quitCourse").addClass("btn-post");
          $("#quitCourse").html('<?= $translates["addcourse"] ?>');
          $("#quitCourse").attr("id", "enrollCourse");
          $("#courseAttandance").html(result.attandance);
        }
      }
    });
  });

  // İtiraf Sayfası

  $("#visibilityOpt").on("change", function() {
    var visibility = $("#visibilityOpt").val();
    if (visibility == 1) {
      var orjpp = $("#ppOrj").attr("pp");
      $("#profileImage").attr("src", "images_profile/" + orjpp);
    } else if (visibility == 2) {
      $("#profileImage").attr("src", "images_profile/profilemale.png");
    }
  });

  $("#form_confession").on("submit", function() {
    var Text = $("#text_confession").val();
    if (!Text) {
      location.reload();
    } else {
      $("#spinnercnfn").html('<i class="fas fa-spinner fa-spin"></i>');
      $("#submitconfession").prop("disabled", true);
      var Visibility = $("#visibilityOpt").val();
      var Topic = $("#topicOpt").val();
      var Datas = {
        "Text": Text,
        "Visibility": Visibility,
        "Topic": Topic
      };
      $.ajax({
        type: "POST",
        url: SITE_URL + "/socialmedia/ajaxconfessions.php?operation=confessit",
        dataType: "json",
        data: Datas,
        success: function(result) {
          location.reload();
        }
      });
    }
  });

  $("#containerConfessions").on("click", ".saveedit", function() {
    var CnfnID = $(this).attr("idsi");
    $("#spinnercnfnedit").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#saveedit_" + CnfnID).prop("disabled", true);
    var Text = $("#edittedtext_" + CnfnID).val();
    var Visibility = $("#edittedVisibilityOpt_" + CnfnID).val();
    var Topic = $("#edittedTopicOpt_" + CnfnID).val();
    var Datas = {
      "CnfnID": CnfnID,
      "Text": Text,
      "Visibility": Visibility,
      "Topic": Topic
    };
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxconfessions.php?operation=editconfession",
      dataType: "json",
      data: Datas,
      success: function(result) {
        $("#spinnercnfnedit").html('');
        $("#saveedit_" + CnfnID).prop("disabled", false);
        location.reload();
      }
    });
  });

  // $(function() {

  //   $.timeCounter = function() {
  //     $.ajax({
  //       type: "post",
  //       url: SITE_URL + "/socialmedia/ajaxtimecounter.php",
  //       data: {
  //         "MemberID": ID
  //       },
  //       success: function(result) {
  //         $("#sayac").html(result);
  //       }
  //     });
  //   }
  // });
  // setInterval('$.timeCounter()', 1000);


  // Change Language AJAX
  function ChangeLang(ToLang, Page, Part, Edit) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxchangelanguage.php",
      dataType: "json",
      data: {
        'toLang': ToLang,
        'currentpage': Page,
        'currentpart': Part
      },
      success: function(result) {
        if (Edit) {
          window.location.href = "http://localhost/aybu/socialmedia/" + result.currentpage + "/" + result.currentpart + "/" + Edit;
        } else {
          if (Part) {
            window.location.href = "http://localhost/aybu/socialmedia/" + result.currentpage + "/" + result.currentpart;
          } else {
            window.location.href = "http://localhost/aybu/socialmedia/" + result.currentpage;
          }
        }
      }
    });
  }

  // Enter Key Events
  $(function() {
    $("#search_person").on("keypress", function(e) {
      if (e.which == 13) {
        e.preventDefault();
        window.location.href = $("#searchfriendicon").attr("href");
      }
    })
  });

  // $("#matchMembers").on("click", function() {
  //   $.ajax({
  //     type: "post",
  //     url: SITE_URL + "/socialmedia/ajaxmatchmembers.php",
  //     dataType: "json",
  //     success: function(result) {
  //       alert("Kişiler eşleştirildi");
  //       location.reload();
  //     }
  //   });
  // });
</script>