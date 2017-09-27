#! /bin/bash

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
PLUGIN_SRC_PATH="$PROJECT_ROOT"
PLUGIN_BUILDS_PATH="$PROJECT_ROOT/build"
PLUGIN_DIST_DIR="dist"
PLUGIN_DIST_PATH="$PLUGIN_BUILDS_PATH/$PLUGIN_DIST_DIR"
PLUGIN_BUILD_CONFIG_PATH="$PLUGIN_BUILDS_PATH/build-cfg"
PLUGIN_BUILD_SCRIPT_PATH="$PLUGIN_BUILDS_PATH/plugin-build"

function echog() {
    echo "$(tput setaf 2)$1$(tput sgr 0)"
}
function echor() {
    echo "$(tput setaf 1)$1$(tput sgr 0)"
}

cd "$PLUGIN_BUILDS_PATH";

echog "Clearing previously built plugins..."
rm -rf "$PLUGIN_DIST_PATH"

if [ ! -d "$PLUGIN_DIST_PATH" ]
then
    mkdir "$PLUGIN_DIST_DIR"
fi

if [ ! -f "$PLUGIN_BUILD_SCRIPT_PATH" -o ! -x "$PLUGIN_BUILD_SCRIPT_PATH" ]
then
    echog "Downloading plugin-build script..."
    curl -sSL https://raw.githubusercontent.com/deliciousbrains/wp-plugin-build/582fdeb3f6d19ae0b1f2bd0da9b48f45c131ac34/plugin-build -o "$PLUGIN_BUILD_SCRIPT_PATH"
    chmod +x "$PLUGIN_BUILD_SCRIPT_PATH"
fi

function build_plugin() {
    PLUGIN_DIR="$PLUGIN_BUILD_CONFIG_PATH/$PLUGIN"

    if [[ -d "$PLUGIN_DIR" && ! -L "$PLUGIN_DIR" ]]; then
        if [ "utils" != "$PLUGIN" ] && [ "common" != "$PLUGIN" ];
        then
            VERSION=$(php -f "$PLUGIN_BUILD_CONFIG_PATH/utils/get_plugin_version.php" "$PROJECT_ROOT" $PLUGIN)
            ZIP_NAME=$(php -f "$PLUGIN_BUILD_CONFIG_PATH/utils/get_plugin_zip_name.php" "$PROJECT_ROOT" $PLUGIN)
            BUILD_ZIP="$PLUGIN_BUILDS_PATH/$ZIP_NAME-$VERSION.zip";

            if [ -f "$BUILD_ZIP" ]
            then
                rm "$BUILD_ZIP"
            fi

            echog "Building $PLUGIN v$VERSION..."
            cd "$PLUGIN_BUILD_CONFIG_PATH/$PLUGIN/"
            "$PLUGIN_BUILDS_PATH/plugin-build" "$VERSION"
            echog "Plugin built: $BUILD_ZIP"
            echo "--------------------------"
        fi
    else
        echo "$PLUGIN_DIR does not exist."
    fi
}

PLUGIN=$(basename "$PROJECT_ROOT");
build_plugin
