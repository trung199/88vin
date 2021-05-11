'use strict';

const JavaScriptObfuscator = require('webpack-obfuscator');

module.exports = {
    entry: {
        'v88': './v88.js'
    },
    output: {
        path: 'D:/88vin/onlineNode/build',
        filename: '[name].js' // output: abc.js, cde.js
    },
    plugins: [
        new JavaScriptObfuscator({
            rotateStringArray: true
        }, ['bundle.js'])
    ]
};