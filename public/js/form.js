$(document).ready(function () {
    let jsonData;
    const localiteOptions = $("#search_localite option")
    const cpOptions = $("#search_cp option")
    const communeOptions = $("#search_commune option")
    $('#search_cp').change(function () {
            let postalCodeId = $(this).val();
            let postalCodeText = $(this).find('option:selected').text();
            $.getJSON('/json/zipcode.json', function (data) {
                postalCodeText = postalCodeText.toString();
                jsonData = data;
                let zipData = jsonData.filter(function (x) {
                    return x.codePostal === postalCodeText
                });

                //let options = initOptions
                /* for (let i = 0; i < options.length; i++) {
                     let option = options[i]
                     let region = option.text.normalize('NFD').replace(/[\u0300-\u036f]/g, "").replace(/-/g, " ").toLowerCase();
                     let dataRegion = zipData[0]['region'].normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase();
                     if (region === dataRegion) {
                         $('#search_localite option[value="' + option.value + '"]').prop('selected', true);
                     }
                 }*/
                $('#search_commune option').remove();
                //$('#search_localite option').remove();

                for (let i = 0; i < zipData.length; i++) {
                    let dataCp = zipData[i].codePostal;
                    let dataCommune = zipData[i].ville.replace(/ /g, "-").toLowerCase()
                    let dataLocalite = zipData[i].region.toLowerCase()
                    for (let j = 0; j < communeOptions.length; j++) {
                        let commune = communeOptions[j].text.toLowerCase()
                        if (dataCommune === commune) {
                            $('#search_commune').append($("<option></option>").attr("value", communeOptions[j].value).text(communeOptions[j].text))

                        }
                    }
                    for (let j = 0; j < localiteOptions.length; j++) {
                        let localite = localiteOptions[j].text.toLowerCase()
                        if (localite === dataLocalite) {
                            console.log(localiteOptions[j].value)
                            $('#search_localite option[value="' + localiteOptions[j].value + '"]').prop('selected', true);
                        }
                    }
                }

            })
        }
    )
    $('#search_localite').change(function () {
        let localiteId = $(this).val();
        let localiteText = $(this).find('option:selected').text();
        $.getJSON('/json/zipcode.json', function (data) {
            jsonData = data
            let zipData = jsonData.filter(function (x) {
                let regionData = x.region.replace(/ /g, "-").toLowerCase()
                return regionData === localiteText.toLowerCase();
            });
            $('#search_cp option').remove();
            $('#search_commune option').remove();
            for (let i = 0; i < zipData.length; i++) {
                let dataCp = zipData[i].codePostal;
                let dataCommune = zipData[i].ville.toLowerCase()

                for (let j = 0; j < cpOptions.length; j++) {
                    if (cpOptions[j].text === dataCp) {
                        $('#search_cp').append($("<option></option>").attr("value", cpOptions[j].value).text(cpOptions[j].text))
                    }
                }
                for (let j = 0; j < communeOptions.length; j++) {
                    let commune = communeOptions[j].text.toLowerCase()
                    if (dataCommune === commune) {
                        $('#search_commune').append($("<option></option>").attr("value", communeOptions[j].value).text(communeOptions[j].text))
                    }
                }
            }

        })
    })
    $('#search_commune').change(function () {
        let communeId = $(this).val()
        let communeText = $(this).find('option:selected').text()
        $.getJSON('/json/zipcode.json', function (data) {
            jsonData = data
            let zipData = jsonData.filter(function (x) {
                let communeData = x.ville.replace(/ /g, "-").toLowerCase()
                console.log(communeText.toLowerCase())
                return communeData === communeText.toLowerCase()
            })
            $('#search_cp option').remove();
            for (let i = 0; i < zipData.length; i++){
                let dataCp = zipData[i].codePostal;
                console.log(zipData[i])
                let dataCommune = zipData[i].ville.replace(/ /g, "-").toLowerCase()
                let dataLocalite = zipData[i].region.toLowerCase()
                console.log(dataCp)
                for (let j = 0; j < cpOptions.length; j++) {
                    if (cpOptions[j].text === dataCp) {
                        $('#search_cp').append($("<option></option>").attr("value", cpOptions[j].value).text(cpOptions[j].text))
                    }
                }
                for (let j = 0; j < localiteOptions.length; j++) {
                    let localite = localiteOptions[j].text.toLowerCase()
                    if (localite === dataLocalite) {
                        console.log(localiteOptions[j].value)
                        $('#search_localite option[value="' + localiteOptions[j].value + '"]').prop('selected', true);
                    }
                }
            }
        })
    })
})
