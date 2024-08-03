function explodeMulti(jsnMulti) {
    // Checking the type to see if it's a multipolygon
    if (jsnMulti.type.substring(0,5) != "Multi") {
        alert("Geometry is not multipart");
    } else {
        // If it is
        let features = [];
        // Making so it can work with any multi-feature. We are setting to everything that comes after the 5th character
        const type = jsnMulti.type.substring(5);
        for (let i = 0; i < jsnMulti.coordinates.length; i++) {
            // this is creating a polygon from the 1st element. Now we are creating a new feature for every polygon in the multi coordinates
            const feature = {type: type, coordinates:jsnMulti.coordinates[i]};
            // we are going to push the features into the feature array. We take an empty array and place our coordinates inside
            features.push(feature)
        }
        return features
    }
}

function mergeLyrEdit(lyrEdit) {
    const jsnEdited = lyrEdit.toGeoJSON();
    let arCoordinates = [];
    // checking the type
    const type = "Multi"+jsnEdited.features[0].geometry.type;
    // looping threw our features
    for (let i = 0; i < jsnEdited.features.length; i++) {
            const coordinates = jsnEdited.features[i].geometry.coordinates;
            arCoordinates.push(coordinates);
    }
    // merging all into a single feature
    return {type: type, coordinates: arCoordinates};
}