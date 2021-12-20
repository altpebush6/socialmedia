<script>
  var SITE_URL = "http://localhost/aybu";
  var ID = <?php
            if ($memberid) {
              echo $memberid;
            } else {
              echo "none";
            }
            ?>;

  // Chatboxı düzenle
  function editChatBox(ChatboxID, FormID) {
    $("#editchatbox_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editChatBox_" + ChatboxID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=editchatbox&ChatboxID=" + ChatboxID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#editchatbox_spinner").html("");
        $("#editChatBox_" + ChatboxID).prop("disabled", false);
        $("#span_FromID_" + ChatboxID).html(result.FromID);
        $("#span_ToID_" + ChatboxID).html(result.ToID);
        $("#span_MessageStatus_" + ChatboxID).html(result.MessageStatus);
        $('.input_' + ChatboxID).removeClass("d-inline-table");
        $('.input_' + ChatboxID).addClass("d-none");
        $('.span_' + ChatboxID).removeClass("d-none");
        $('.span_' + ChatboxID).addClass("d-inline-table");
        $('#editChatBox_' + ChatboxID).removeClass("d-none");
        $('#editChatBox_' + ChatboxID).addClass("d-inline-table");
        $('.editbtn_' + ChatboxID).removeClass("d-inline-table");
        $('.editbtn_' + ChatboxID).addClass('d-none');
        if (result.MessageStatus != 1) {
          $("#chatbox_info_" + ChatboxID).removeClass("bg-light text-dark");
          $("#chatbox_info_" + ChatboxID).addClass("bg-danger text-light");
        } else {
          $("#chatbox_info_" + ChatboxID).removeClass("bg-danger text-light");
          $("#chatbox_info_" + ChatboxID).addClass("bg-light text-dark");
        }
      }
    });
  }

  function deleteChatBox(ChatboxID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=delchatbox",
      dataType: "json",
      data: {
        'ChatboxID': ChatboxID
      },
      success: function(result) {
        if (result.success == "ok") {
          $("#chatbox_info_" + ChatboxID).addClass("bg-danger text-light");
          $("#span_MessageStatus_" + ChatboxID).html(0);
          $("#MessageStatusInput_" + ChatboxID).val(0);
        }
      }
    });
  }

  // Mesajları düzenle
  function editMessage(MessageID, FormID) {
    $("#messageedit_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editMessage_" + MessageID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=editmessage&MessageID=" + MessageID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#messageedit_spinner").html("");
        $("#editMessage_" + MessageID).prop("disabled", false);
        $("#span_MessageText_" + MessageID).html(result.MessageText);
        $("#span_MessageImg_" + MessageID).html(result.MessageImg);
        $("#span_MessageFromID_" + MessageID).html(result.MessageFromID);
        $("#span_MessageToID_" + MessageID).html(result.MessageToID);
        $("#span_MessagesStatus_" + MessageID).html(result.MessageStatus);
        $('.input_' + MessageID).removeClass("d-inline-table");
        $('.input_' + MessageID).addClass("d-none");
        $('.span_' + MessageID).removeClass("d-none");
        $('.span_' + MessageID).addClass("d-inline-table");
        $('#editMessage_' + MessageID).removeClass("d-none");
        $('#editMessage_' + MessageID).addClass("d-inline-table");
        $('.editbtn_' + MessageID).removeClass("d-inline-table");
        $('.editbtn_' + MessageID).addClass('d-none');
        if (result.MessageStatus != 1) {
          $("#message_info_" + MessageID).removeClass("bg-light text-dark");
          $("#message_info_" + MessageID).addClass("bg-danger text-light");
        } else {
          $("#message_info_" + MessageID).removeClass("bg-danger text-light");
          $("#message_info_" + MessageID).addClass("bg-light text-dark");
        }
      }
    });
  }

  function deleteMessage(MessageID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=delmessage",
      dataType: "json",
      data: {
        'MessageID': MessageID
      },
      success: function(result) {
        if (result.success == "ok") {
          $("#message_info_" + MessageID).addClass("bg-danger text-light");
          $("#span_MessagesStatus_" + MessageID).html(0);
          $("#MessagesStatusInput_" + MessageID).val(0);
        }
      }
    });
  }

  // Resimleri düzenle

  function editImage(ImgID, FormID) {
    $("#images_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editimg_" + ImgID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=editimage&ImgID=" + ImgID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#images_spinner").html("");
        $("#editimg_" + ImgID).prop("disabled", false);
        $("#span_Profileimg_" + ImgID).html(result.ProfileImg);
        $("#span_Coverimg_" + ImgID).html(result.CoverImg);
        $('.input_' + ImgID).removeClass("d-inline-table");
        $('.input_' + ImgID).addClass("d-none");
        $('.span_' + ImgID).removeClass("d-none");
        $('.span_' + ImgID).addClass("d-inline-table");
        $('#editimg_' + ImgID).removeClass("d-none");
        $('#editimg_' + ImgID).addClass("d-inline-table");
        $('.editbtn_' + ImgID).removeClass("d-inline-table");
        $('.editbtn_' + ImgID).addClass('d-none');
      }
    });
  }

  // Hakkındaları düzenle

  function editAbout(AboutID, FormID) {
    $("#abouts_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editabout_" + AboutID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=editabout&AboutID=" + AboutID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#abouts_spinner").html("");
        $("#editabout_" + AboutID).prop("disabled", false);
        $("#span_Faculty_" + AboutID).html(result.Faculty);
        $("#span_Department_" + AboutID).html(result.Department);
        $("#span_Hobbies_" + AboutID).html(result.Hobbies);
        $("#span_FavTV_" + AboutID).html(result.FavTV);
        $("#span_Hometown_" + AboutID).html(result.Hometown);
        $("#span_City_" + AboutID).html(result.City);
        $('.input_' + AboutID).removeClass("d-inline-table");
        $('.input_' + AboutID).addClass("d-none");
        $('.span_' + AboutID).removeClass("d-none");
        $('.span_' + AboutID).addClass("d-inline-table");
        $('#editabout_' + AboutID).removeClass("d-none");
        $('#editabout_' + AboutID).addClass("d-inline-table");
        $('.editbtn_' + AboutID).removeClass("d-inline-table");
        $('.editbtn_' + AboutID).addClass('d-none');
      }
    });
  }

  // Hakkında Talepleri
  function editRequest(RequestID, FormID) {
    $("#requests_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editRequest_" + RequestID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=editRequest&RequestID=" + RequestID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#requests_spinner").html("");
        $("#editRequest_" + RequestID).prop("disabled", false);
        $('.input_' + RequestID).removeClass("d-inline-table");
        $('.input_' + RequestID).addClass("d-none");
        $('.span_' + RequestID).removeClass("d-none");
        $('.span_' + RequestID).addClass("d-inline-table");
        $('#editRequest_' + RequestID).removeClass("d-none");
        $('#editRequest_' + RequestID).addClass("d-inline-table");
        $('.editbtn_' + RequestID).removeClass("d-inline-table");
        $('.editbtn_' + RequestID).addClass('d-none');
        $("#span_MemberID_" + RequestID).html(result.MemberID);
        $("#span_RequestItem_" + RequestID).html(result.RequestItem);
        $("#span_RequestStatus_" + RequestID).html(result.RequestStatus);
        if (result.RequestStatus != 1) {
          $("#request_info_" + RequestID).removeClass("bg-light text-dark");
          $("#request_info_" + RequestID).addClass("bg-danger text-light");
        } else {
          $("#request_info_" + RequestID).removeClass("bg-danger text-light");
          $("#request_info_" + RequestID).addClass("bg-light text-dark");
        }
      }
    });
  }

  function deleteRequest(RequestID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxgeneral.php?operation=delRequest",
      dataType: "json",
      data: {
        'RequestID': RequestID
      },
      success: function(result) {
        if (result.success == "ok") {
          $("#request_info_" + RequestID).remove();
        }
      }
    });
  }

  // Kullanıcıları düzenle
  function editMember(MemberID, FormID) {
    $("#edit_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editMember_" + MemberID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxmemberoperations.php?operation=editMember&MemberID=" + MemberID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#edit_spinner").html("");
        $("#editMember_" + MemberID).prop("disabled", false);
        $('.input_' + MemberID).removeClass("d-inline-table");
        $('.input_' + MemberID).addClass("d-none");
        $('.span_' + MemberID).removeClass("d-none");
        $('.span_' + MemberID).addClass("d-inline-table");
        $('#editMember_' + MemberID).removeClass("d-none");
        $('#editMember_' + MemberID).addClass("d-inline-table");
        $('.editbtn_' + MemberID).removeClass("d-inline-table");
        $('.editbtn_' + MemberID).addClass('d-none');
        $("#span_Email_" + MemberID).html(result.email);
        $("#span_Names_" + MemberID).html(result.names);
        $("#span_Gender_" + MemberID).html(result.gender);
        $("#span_Confirm_" + MemberID).html(result.confirm);
        if (result.confirm != 1) {
          $("#member_info_" + MemberID).removeClass("bg-light text-dark");
          $("#member_info_" + MemberID).addClass("bg-danger text-light");
        } else {
          $("#member_info_" + MemberID).removeClass("bg-danger text-light");
          $("#member_info_" + MemberID).addClass("bg-light text-dark");
        }
      }
    });
  }

  function deleteMember(MemberID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmemberoperations.php?operation=delMember",
      dataType: "json",
      data: {
        'MemberID': MemberID
      },
      success: function(result) {
        if (result.success == "ok") {
          $("#member_info_" + MemberID).addClass("bg-danger text-light");
          $("#span_Confirm_" + MemberID).html(2);
          $("#input_Confirm_" + MemberID).val(2);
        }
      }
    });
  }

  // Admineri düzenle
  function editAdmin(AdminID, FormID) {
    $("#edit_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#editAdmin_" + AdminID).prop("disabled", true);
    var Datas = $("#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxmemberoperations.php?operation=editAdmin&AdminID=" + AdminID,
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#edit_spinner").html("");
        $("#editAdmin_" + AdminID).prop("disabled", false);
        $('.input1_' + AdminID).removeClass("d-inline-table");
        $('.input1_' + AdminID).addClass("d-none");
        $('.span1_' + AdminID).removeClass("d-none");
        $('.span1_' + AdminID).addClass("d-inline-table");
        $('#editAdmin_' + AdminID).removeClass("d-none");
        $('#editAdmin_' + AdminID).addClass("d-inline-table");
        $('.editbtn1_' + AdminID).removeClass("d-inline-table");
        $('.editbtn1_' + AdminID).addClass('d-none');
        $("#span_Email_" + AdminID).html(result.email);
        $("#span_Names_" + AdminID).html(result.names);
        $("#span_Gender_" + AdminID).html(result.gender);
        $("#span_Confirm_" + AdminID).html(result.confirm);
        if (result.confirm != 1) {
          $("#admin_info_" + AdminID).removeClass("bg-light text-dark");
          $("#admin_info_" + AdminID).addClass("bg-danger text-light");
        } else {
          $("#admin_info_" + AdminID).removeClass("bg-danger text-light");
          $("#admin_info_" + AdminID).addClass("bg-light text-dark");
        }
      }
    });
  }

  function deleteAdmin(AdminID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxmemberoperations.php?operation=delAdmin",
      dataType: "json",
      data: {
        'AdminID': AdminID
      },
      success: function(result) {
        if (result.success == "ok") {
          $("#admin_info_" + AdminID).addClass("bg-danger text-light");
          $("#span_Confirm_" + AdminID).html(2);
          $("#input_Confirm_" + AdminID).val(2);
        }
      }
    });
  }

  // Gönderileri düzenle
  function RemovePostReport(Operation, PostID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?PostID=" + PostID + "&operation=" + Operation,
      dataType: "text",
      success: function(result) {
        $("#repCounter_" + PostID).remove();
        $("#reppostid_" + PostID).remove();
      }
    });
  }

  function RemoveCommentReport(Operation, PostID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?PostID=" + PostID + "&operation=" + Operation,
      dataType: "text",
      success: function(result) {
        $("#repCounter_" + PostID).remove();
        $("#reppostid_" + PostID).remove();
      }
    });
  }

  function DeletePost(Operation, PostID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?PostID=" + PostID + "&operation=" + Operation,
      dataType: "text",
      success: function(result) {
        $("#postid_" + PostID).remove();
        $("#reppostid_" + PostID).remove();
      }
    });
  }

  function DeleteComment(Operation, CommentID, PostID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxposts.php?CommentID=" + CommentID + "&operation=" + Operation,
      dataType: "text",
      success: function(result) {
        $(".each_comment_" + CommentID).remove();
        $(".comment_label_" + PostID).html("<?= $translates["commentpost"] ?> (" + result + ")");
      }
    });
  }
  // Konuları düzenle
  function addTopic() {
    $("#topic_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#addTopic").prop("disabled", true);
    var topicName = $("#new_topic").val();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxtopics.php?Operation=addTopic",
      data: {
        'topicName': topicName
      },
      dataType: "json",
      success: function(result) {
        $("#new_topic").val("");
        $("#topic_spinner").html('');
        $("#addTopic").prop("disabled", false);
        $("#topics_ul").append(result.newTopic);
      }
    });
  }

  function deleteTopic(TopicID) {
    $.ajax({
      type: "POST",
      url: SITE_URL + "/socialmedia/ajaxtopics.php?Operation=delTopic",
      dataType: "json",
      data: {
        'TopicID': TopicID
      },
      success: function(result) {
        $(".topic_" + TopicID).remove();
      }
    });
  }

  $(function() {
    $("#topics_ul").sortable({
      cursor: "move",
      opacity: 0.8,
      update: function(event, ui) {
        var myData = $(this).sortable("serialize");
        $.ajax({
          type: "POST",
          url: "http://localhost/aybu/socialmedia/ajaxtopics.php?Operation=sortTopics",
          dataType: "text",
          data: {
            'list': myData
          },
          success: function(result) {
            $("#topics_ul").html(result);
          }
        });
      }
    });
    $("#topics_ul").disableSelection();
  });

  // Haber işlemleri
  $("#allnews").on("change", ".newsActiveness", function() {
    var isChecked = $(this).prop("checked");
    if (isChecked) {
      var Operation = "openNews";
    } else {
      var Operation = "closeNews";
    }
    var NewsID = $(this).attr("newsID");
    $.ajax({
      type: "POST",
      url: "http://localhost/aybu/socialmedia/ajaxnews.php?operation=" + Operation,
      dataType: "json",
      data: {
        'NewsID': NewsID
      }
    });
  });

  $("#showmorebtn").on("click", function(e) {
    var LastID = $(this).attr("lastid");
    e.preventDefault();
    $("#showmorenews").html('<i class="fas fa-4x fa-spinner fa-spin"></i>');
    $("#showmorebtn").addClass("d-none");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=showNews",
      data: {
        'LastID': LastID
      },
      dataType: "json",
      success: function(result) {
        $("#showmorenews").html("");
        $("#showmorebtn").prop("disabled", false);
        $("#allnews").append(result.news);
        $("#showmorebtn").attr("lastid", result.lastid);
        if (result.newsremain > 0) {
          $("#showmorebtn").removeClass("d-none");
          $("#showmorebtn").addClass("d-block");
        }
      }
    });
  });

  $("#newsForm").on('submit', function(e) {
    e.preventDefault();
    $("#news_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#submitNews").prop("disabled", true);
    var Datas = new FormData(this);
    Datas.append("Newsheader", $("#Newsheader").val());
    Datas.append("Newsauthor", $("#Newsauthor").val());
    Datas.append("NewsResource", $("#NewsResource").val());
    Datas.append("NewsContentSummarize", $("#NewsContentSummarize").val());
    Datas.append("NewsContent", CKEDITOR.instances.NewsContent.getData());
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=createNews",
      data: Datas,
      dataType: "json",
      contentType: false,
      cache: false,
      processData: false,
      success: function(result) {
        $("#news_spinner").html("");
        $("#submitNews").prop("disabled", false);
        $("#newsForm").trigger("reset");
        $(".news_order").append('<option class="py-1 px-2" value="'+result.newsOrder+'" id="option_'+result.newsID+'_'+result.newsOrder+'">'+result.newsOrder+'</option>');
        if (result.countNews < 4) {
          $("#allnews").append(result.news);
        }
      }
    });
  })

  $("#allContainer").on("click", ".editnews", function() {
    var NewsID = $(this).attr("newsid");
    $("#editnewsspinner_" + NewsID).html('<i class="fas fa-spinner fa-spin"></i>');
    $(this).prop("disabled", true);
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=editnews",
      data: {
        'NewsID': NewsID
      },
      dataType: "json",
      success: function(result) {
        $("#editnewsspinner_" + NewsID).html("");
        $(this).prop("disabled", false);
        $("#containerNews").html(result.output);
      }
    });
  });

  $("#allContainer").on('submit', "#EditnewsForm", function(e) {
    e.preventDefault();
    $("#editnews_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#EditsubmitNews").prop("disabled", true);
    var NewsID = $(this).attr("newsid");
    var Datas = new FormData(this);
    Datas.append("EditNewsheader", $("#EditNewsheader").val());
    Datas.append("EditNewsauthor", $("#EditNewsauthor").val());
    Datas.append("EditNewsResource", $("#EditNewsResource").val());
    Datas.append("EditNewsContentSummarize", $("#EditNewsContentSummarize").val());
    Datas.append("EditNewsContent", $("#EditNewsContent").val());
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=submitedittednews&NewsID=" + NewsID,
      data: Datas,
      dataType: "json",
      contentType: false,
      cache: false,
      processData: false,
      success: function(result) {
        $("#editnews_spinner").html("");
        $("#EditsubmitNews").prop("disabled", false);
        $("#editnewsHeader").html(result.EdittedNewsHeader);
        $("#editnewsImg").attr("src", "news_images/" + result.EdittedNewsImg);
        $("#editnewsContent").html(result.EdittedNewsContent);
        $("#editnewsAuthor").html(result.EdittedNewsauthor);
        $("#editnewsResource").html(result.EdittedNewsResource);
      }
    });
  });

  $("#allnews").on('change', ".news_order", function(e) {
    var newOrder = $(this).val();
    var NewsID = $(this).attr("newsid");
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=changeorder&NewValNewsID=" + NewsID,
      data: {
        "NewOrder": newOrder,
        "NewsID": NewsID
      },
      dataType: "json",
      success: function(result) {
        if (result.error) {
          alert("Bir Hata Oluştu!");
        } else {
          $("#news_order_" + result.otherNewsID).val(result.oldOrder);
          $("#option_" + result.otherNewsID + "_" + newOrder).prop("disabled", false);
          $("#option_" + result.otherNewsID + "_" + newOrder).prop("selected", false);
          $("#option_" + result.otherNewsID + "_" + result.oldOrder).prop("disabled", true);
          $("#option_" + result.otherNewsID + "_" + result.oldOrder).prop("selected", true);
          $("#option_" + NewsID + "_" + newOrder).prop("disabled", true);
          $("#option_" + NewsID + "_" + newOrder).prop("selected", true);
          $("#option_" + NewsID + "_" + result.oldOrder).prop("disabled", false);
          $("#option_" + NewsID + "_" + result.oldOrder).prop("selected", false);
        }
      }
    });
  });

  function deleteNews(NewsID) {
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxnews.php?operation=deletenews&NewsID=" + NewsID,
      dataType: "json",
      success: function(result) {
        $("#News_" + NewsID).remove();
      }
    });
  }

  //Şifre
  function SendFormPass(FormID) {
    $("#pass_spinner").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#submitpassword").prop("disabled", true);
    var Datas = $("form#" + FormID).serialize();
    $.ajax({
      type: "post",
      url: SITE_URL + "/socialmedia/ajaxpassword.php",
      data: Datas,
      dataType: "json",
      success: function(result) {
        $("#pass_spinner").html("");
        $("#submitpassword").prop("disabled", false);
        if (result.error) {
          $("#pass-result-admin").addClass("bg-danger text-light p-3 mb-3 w-75 mx-auto text-center rounded-3");
          $("#pass-result-admin").html("<i class='fas fa-exclamation-triangle'></i> " + result.error);
          $("#pass_old_admin").css("border", "");
          $("#pass_new_admin").css("border", "");
          $("#pass_new_again_admin").css("border", "");
          $(result.errorinput1).css("border", "2px solid red");
          $(result.errorinput2).css("border", "2px solid red");
          $(result.errorinput3).css("border", "2px solid red");
        }
        if (result.success) {
          $("form").trigger("reset");
          $("#pass-result-admin").removeClass("bg-danger");
          $("#pass-result-admin").addClass("bg-success text-light p-3 mb-3 w-75 mx-auto text-center rounded-3");
          $("#pass-result-admin").html('<i class="fas fa-check-square"></i> ' + result.success);
          $("#pass_old_admin").css("border", "");
          $("#pass_new_admin").css("border", "");
          $("#pass_new_again_admin").css("border", "");
        }
      }
    });
  }
</script>