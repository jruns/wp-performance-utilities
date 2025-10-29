document.addEventListener('DOMContentLoaded', function () {
    var youtubeFacades = document.querySelectorAll('.perfutils-youtube-embed a');
    youtubeFacades.forEach(function (facade) {
        facade.addEventListener('click', function (e) {
			e.preventDefault();

            var iframe = document.createElement('iframe');
            iframe.setAttribute('src', 'https://www.youtube.com/embed/' + facade.dataset.videoId + '?autoplay=1');
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('width', facade.dataset.width ?? 560);
            iframe.setAttribute('height', facade.dataset.height ?? 315);
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
			iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
            facade.replaceWith(iframe);
            iframe.focus();
        });
    });
});