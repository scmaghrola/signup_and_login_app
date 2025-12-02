import './bootstrap';

// Optional: require the app.css if you compile it via your build step
try {
	require('../css/app.css');
} catch (e) {
	// ignore in case CSS is not bundled
}
