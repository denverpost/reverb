
    function listrakActions() {
        var getNewsletterID = 25; // hardcoded for the know newsletter
        var userEmail = document.getElementById('userEmail').value;
        console.log(getNewsletterID + " ----- " + userEmail);
          var params =  {
              email: userEmail, // subscriber email
              viewMode: 1, // 1: tabs view mode, 2: list view mode
              newsletterIdsList: [getNewsletterID]
          };
          mg2WidgetAPI.openNewsletter(params);

    }
