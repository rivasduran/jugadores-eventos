//Function for converting from CSV to JSON. This function is consider as a backend component for performing this task.
var csvjsonConverter = (csvdata, delimiter) => {
    //This array will store the each of the patterns from the regular expression below.
    let arrmatch = [];

    //This array will store the data from the CSV.
    let array = [[]];

    //Stores matched values for quoted values.
    let quotevals = "";

    //Storing JSON array
    let jsonarray = [];

    //Increment value
    let k = 0;

    //Uses regular expression to parse the CSV data and determines if any values has their own quotes in case if any
    // delimiters are within.
    let regexp = new RegExp(("(\\" + delimiter + "|\\r?\\n|\\r|^)" + "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
        "([^\"\\" + delimiter + "\\r\\n]*))"), "gi");

    //This will loop to find any matchings with the regular expressions.
    while (arrmatch = regexp.exec(csvdata)) {
        //This will determine what the delimiter is.
        let delimitercheck = arrmatch[1];
        //Matches the delimiter and determines if it is a row delimiter and matches the values to the first rows.
        //If it reaches to a new row, then an empty array will be created as an empty row in array.
        if ((delimitercheck !== delimiter) && delimitercheck.length) {
            array.push([]);
        }

        //This determines as to what kind of value it is whether it has quotes or not for these conditions.
        if (arrmatch[2]) {
            quotevals = arrmatch[2].replace('""', '\"');
        }
        else {
            quotevals = arrmatch[3];
        }

        //Adds the value from the data into the array
        array[array.length - 1].push(quotevals);
    }

    //This will parse the resulting array into JSON format
    for (let i = 0; i < array.length - 1; i++) {
        jsonarray[i - 1] = {};
        for (let j = 0; j < array[i].length && j < array[0].length; j++) {
            let key = array[0][j];
            jsonarray[i - 1][key] = array[i][j]
        }
    }

    //This will determine what the properties of each values are from the JSON
    //such as removing quotes for integer value.
    for(k = 0; k < jsonarray.length; k++){
        let jsonobject = jsonarray[k];
        for(let prop in jsonobject){
            if(!isNaN(jsonobject[prop]) && jsonobject.hasOwnProperty(prop)){
                jsonobject[prop] = +jsonobject[prop];
            }
        }
    }

    //This will stringify the JSON and formatting it.
    let formatjson = JSON.stringify(jsonarray, null, 2);

    //Returns the converted result from CSV to JSON
    return formatjson;
};

//This jQuery will perform in the front-end to convert from CSV to JSON.
jQuery(function () {
    //When the 'Convert' button is clicked, it will first make sure if the csv file is uploaded and then it goes to the
    //convert function above to convert it from CSV to JSON. Afterwards, it will print the result in a textarea.
    jQuery("#convert").click(function () {
        var csv = jQuery("#csv")[0].files[0];
        if (csv !== undefined) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var rows = e.target.result;
                var convertjson = csvjsonConverter(rows, jQuery("#delimiter").val());
                jQuery("#json").val(convertjson);

                //LO MANDAMOS A RECORRER
                recorrelas_todas(convertjson);
            };
            reader.readAsText(csv);
        }
        else{
            jQuery("#json").val("");
            alert("Please upload your csv file.");
        }
    });

    //After the user clicks on 'Download JSON Result" button, it will download the converted JSON file.
    jQuery("#download").click(function () {
            var result = jQuery("#json").val();
            if (result === null || result === undefined || result === "") {
                alert("Please make sure there is JSON data in the text area.");
            }
            else {
                jQuery("<a />", {
                    "download": "data.json",
                    "href": "data:application/json;charset=utf-8," + encodeURIComponent(result),
                }).appendTo("body")
                    .click(function () {
                        jQuery(this).remove()
                    })[0].click()
            }
        }
    );
});

//FUNCION QUE RECORRE VARIABLES
function recorrelas_todas(variables, posicion = "1"){
    //console.log(variables);
    /*
    jQuery.each(variables, function(index, item) {
        console.log("pasamos por aqui");
        //jQuery("#mensaje").html(item.data.Name)
    }); 
    */
    /*
    var mensajes = variables;
    jQuery.each( mensajes => function(){
        console.log("llegamos");
    });
    */
    /*
    for (var i = 0; i <= variables.length; i++) {
        console.log(variables[i].Email);
    }
    */
    var variables_f = [];

    var obj = JSON.parse(variables);
    var cual = 0;
    obj.map(rela => {
        cual++;
        if(cual == posicion){
            //VARIABLES
            var email               = rela.Email;
            var elige_tu_equipo     = rela.Elige_tu_equipo;
            var nombre              = rela.Nombre;
            var apellido            = rela.Apellido;
            var apellido_materno    = rela.Apellido_materno;
            var apodo               = rela.Apodo;
            var camisa              = rela.Camisa;

            var momentaneo = {
                email:              email,
                elige_tu_equipo:    elige_tu_equipo,
                nombre:             nombre,
                apellido:           apellido,
                apellido_materno:   apellido_materno,
                apodo:              apodo,
                camisa:             camisa
            };

            variables_f.push(momentaneo);

            momentaneo = {};

            //
            /* ESTE ESTA BUENO SOLO HAY QUE MEJORAR
            var data = {
                'action': 'my_action_jugador',
                'jugadores': momentaneo
            };

            jQuery.post(postlove.ajax_url, data, function(response) {
                //alert('Got this from the server: ' + response);

                console.log(response);

            });
            */
        }
    });

    //
    var data = {
        'action': 'my_action_jugador',
        'jugadores': variables_f,
        'sp_season': jQuery("select[name='sp_season']").val(),
        'sp_league': jQuery("select[name='sp_league']").val()
    };

    jQuery.post(postlove.ajax_url, data, function(response){
        //alert('Got this from the server: ' + response);
        if(posicion <= obj.length){
            posicion++;

            recorrelas_todas(variables, posicion);
        }
        console.log(response);

    });

}