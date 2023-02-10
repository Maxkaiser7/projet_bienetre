$(document).ready(function () {
    let jsonData;
    const initOptions = $("#search_localite option")

    $('#search_cp').change(function () {
        let postalCodeId = $(this).val();
        let postalCodeText = $(this).find('option:selected').text();
        console.log(postalCodeId, postalCodeText)
        $.getJSON('/json/zipcode.json', function (data) {
            postalCodeText = postalCodeText.toString();
            jsonData = data;
            let zipData = jsonData.filter(function (x) {
                return x.codePostal === postalCodeText
            });
            let options = initOptions
            console.log(zipData)
            for (let i = 0; i < options.length; i++) {
                console.log(options)
                let option = options[i]
                let region = option.text.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase();
                let dataRegion = zipData[0]['region'].normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase();
                console.log('!!!')
                console.log(region, dataRegion)
                if (region === dataRegion) {
                    console.log('===')
                    console.log(region, dataRegion)
                    $('#search_localite option').remove();
                    $('#search_localite').append($("<option></option>").attr("value", option.value).text(region));
                }
            }


        })


    })

})
