# Enhanced Dependencies

Enhancements for WordPress dependencies (ex: server push, inlining, async).

## Enhancements
|Enhancement Name|Description|
|---|---|
|[Async](dist/classes/enhancements/async.php)|Add asynchronous loading to the script or stylesheet.|
|[Defer](dist/classes/enhancements/defer.php)|Add `defer` attribute to script tag.|
|[Inline](dist/classes/enhancements/inline.php)|Print the contents of the script or stylesheet inline in the HTML document.|
|[Preconnect](dist/classes/enhancements/preconnect.php)|Establish connection to the domain to improve load time.|
|[Prefetch](dist/classes/enhancements/prefetch.php)|Request and download the the asset and store in cache.|
|[Preload](dist/classes/enhancements/preload.php)|Load the dependency using HTTP2 push or `link` with `preload`.|
|Push|Load the dependency using HTTP2 push, if possible.|