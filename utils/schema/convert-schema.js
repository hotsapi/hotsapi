const yaml = require('js-yaml');
const fs   = require('fs');

function convert(obj) {
    let result = [];

    for (let name in obj) {
        let val = obj[name];
        let data = {
            name: name
        };

        if (Array.isArray(val)) {
            val = val[0];
            data.mode = "REPEATED";
        }

        if (typeof val == 'string') {
            data.type = val.toUpperCase();
        } else {
            data.type = "RECORD";
            data.fields = convert(val);
        }
        result.push(data);
    }

    return result
}

let doc = yaml.safeLoad(fs.readFileSync('schema.yml'));
fs.writeFileSync('schema.replays.json', JSON.stringify(convert(doc.replays), null, 2));
fs.writeFileSync('schema.heroes.json', JSON.stringify(convert(doc.heroes), null, 2));
fs.writeFileSync('schema.maps.json', JSON.stringify(convert(doc.maps), null, 2));
console.log("done");
