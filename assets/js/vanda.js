function vanda(){

}

    vanda.formToString = function (filledForm) {
        formObject = new Object
        filledForm.find("input, select, textarea").each(function() {
            if (this.id) {
                elem = $(this);
                if (elem.attr("type") == 'checkbox' || elem.attr("type") == 'radio') {
                    formObject[this.id] = elem.attr("checked");
                } else {
                    formObject[this.id] = elem.val();
                }
            }
        });

        formString = JSON.stringify(formObject);

        return formString;
    }

    vanda.stringToForm = function (formString, unfilledForm) {

        formObject = JSON.parse(formString);
        unfilledForm.find("input, select, textarea").each(function() {
            if (this.id) {
                id = this.id;
                elem = $(this);
                if (elem.attr("type") == "checkbox" || elem.attr("type") == "radio" ) {
                    elem.attr("checked", formObject[id]);
                } else {
                    elem.val(formObject[id]);
                }
            }
        });
    }