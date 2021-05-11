"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
const javascript_obfuscator_1 = __importDefault(require("javascript-obfuscator"));
const loader_utils_1 = __importDefault(require("loader-utils"));
function Loader(sourceCode) {
    const options = loader_utils_1.default.getOptions(this) || {};
    const obfuscationResult = javascript_obfuscator_1.default.obfuscate(sourceCode, Object.assign(Object.assign({}, options), { ignoreRequireImports: true }));
    return obfuscationResult.getObfuscatedCode();
}
module.exports = Loader;
