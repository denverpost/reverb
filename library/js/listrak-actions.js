
    function listrakActions() {
        var getNewsletterID = "25,26"; // hardcoded for the know newsletter
        var userEmail = document.getElementById('userEmail').value;
          var params =  {
              email: userEmail, // subscriber email
              viewMode: 1, // 1: tabs view mode, 2: list view mode
              newsletterIdsList: [getNewsletterID]
          };
          mg2WidgetAPI.openNewsletter(params);

    }
