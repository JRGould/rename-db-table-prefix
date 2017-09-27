var webpack = require( "webpack" );
var path = require( "path" );
var glob = require( "glob" );
var ExtractTextPlugin = require( "extract-text-webpack-plugin" );
var distMode = ( "dist" === process.env.NODE_ENV );

module.exports = {
	entry: {
		"rdtp-admin": [
			"./admin/src/js/rdtp-admin.js",
			"./admin/src/scss/rdtp-admin.scss"
		]
	},
	output: {
		path: path.resolve( __dirname, "./admin/dist/" ),
		filename: "[name].js"
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: "babel-loader"
			},
			{
				test: /\.s[ac]ss$/, // .sass and .scss files
				exclude: /^_.*/,
				use: ExtractTextPlugin.extract({
					use: [
						{
							loader: 'css-loader',
							options: { url: false }
						},
						"sass-loader"
					],
					fallback: 'style-loader'
				})
			}
		]
	},
	plugins: [
		new ExtractTextPlugin( '[name].css' ),

	    new webpack.LoaderOptionsPlugin({
		     minimize: distMode
	    })
	]
};

if( distMode ) {
	module.exports.plugins.push (
		new webpack.optimize.UglifyJsPlugin()
	);
}
