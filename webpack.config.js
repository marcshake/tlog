const path = require('path');
var copyWebpackPlugin = require('copy-webpack-plugin');

const frontend = {
    mode:"production",
    target: "web",
    entry:'./tf3Framework/resources/frontend.js',
    output: {
        filename: 'tlog.front.js',
        path: path.resolve(__dirname,'public/js')
    },
    "module": {
        "rules": [
            {
                "test": /\.css$/,
                "use": [
                    "style-loader",
                    "css-loader"
                ]
            }
        ],
        
        
    }

};

const backend = 
    {
        "mode": "production",
        "entry": "./tf3Framework/resources/backend.js",
        "output": {
            "path": __dirname+'/public/js',
            "filename": "tlog.back.js"
        },
        plugins: [
            new copyWebpackPlugin([
              { from: './node_modules/tinymce/plugins', to: './plugins' },
              { from: './node_modules/tinymce/themes', to: './themes' },
              { from: './node_modules/tinymce/skins', to: './skins' }
            ])
          ],
        "module": {
            "rules": [
                {
                    "test": /\.css$/,
                    "use": [
                        "style-loader",
                        "css-loader"
                    ]
                }
            ],
            
            
        }
    
};

module.exports = [frontend, backend];
