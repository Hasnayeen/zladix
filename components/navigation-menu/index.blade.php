@props([
    'ref',
    'baseId' => 'navigation-menu',
    'orientation' => 'horizontal',
    'dir' => 'ltr',
    'delayDuration' => 200,
    'skipDelayDuration' => 300,
])

<x-utils>
    <nav
        x-ref="$ref"
        tag="nav"
        aria-label="Main"
        data-orientation="{{ $orientation }}"
        dir="{{ $dir }}"
        x-data="{
            count: 0,
            isRootMenu: true,
            modelValue: null,
            previousValue: null,
            baseId: '{{ $baseId }}',
            dir: '{{ $dir }}',
            orientation: '{{ $orientation }}',
            delayDuration: '{{ $delayDuration }}',
            skipDelayDuration: '{{ $skipDelayDuration }}',
            rootNavigationMenu: $refs['{{ $ref }}'],
            indicatorTrack: null,
            get showOnViewport () {
                return this.modelValue && this.viewport;
            },
            // @fn onIndicatorTrackChange
            onIndicatorTrackChange (val) {
                this.indicatorTrack = val;
            },
            viewport: null,
            // @fn onViewportChange
            onViewportChange (val) {
                this.viewport = val;
            },
            viewportContent: new Map(),
            viewportSize: {
                width: 0,
                height: 0,
            },
            // @fn onViewportContentChange
            onViewportContentChange (contentValue, contentData) {
                const prev = this.viewportContent;
                this.viewportContent = new Map(prev.set(contentValue, contentData));
                this.viewportSize = {
                    width: contentData.offsetWidth,
                    height: contentData.offsetHeight,
                };
            },
            // @fn onViewportContentRemove
            onViewportContentRemove (contentValue) {
                const prev = this.viewportContent;
                if (!prev.has(contentValue)) return prev;
                prev.delete(contentValue);
                this.viewportContent = new Map(prev);
            },
            // @fn onTriggerEnter
            onTriggerEnter (val) {
                this.debounceFn()(val);
            },
            // @fn onTriggerLeave
            onTriggerLeave () {
                this.debounceFn()('');
            },
            // @fn onContentEnter
            onContentEnter (val) {
                this.debounceFn()(val);
            },
            // @fn onContentLeave
            onContentLeave () {
                this.debounceFn()('');
            },
            // @fn onItemSelect
            onItemSelect (val) {
                this.previousValue = this.modelValue;
                this.modelValue = val;
            },
            // @fn debounceFn
            debounceFn () {
                const fn = (val) => {
                    this.previousValue = this.modelValue;
                    this.modelValue = val;
                };
                return debounce(fn, this.delayDuration);
            },
            // @fn removeFromTabOrder
            removeFromTabOrder (candidates) {
                candidates.forEach((candidate) => {
                    candidate.dataset.tabindex = candidate.getAttribute('tabindex') || '';
                    candidate.setAttribute('tabindex', '-1');
                });
                return () => {
                    candidates.forEach((candidate) => {
                        const prevTabIndex = candidate.dataset.tabindex;
                        candidate.setAttribute('tabindex', prevTabIndex);
                    });
                };
            },
            // @fn getTabbableCandidates
            getTabbableCandidates (container) {
                const nodes = [];
                const walker = document.createTreeWalker(
                    container,
                    NodeFilter.SHOW_ELEMENT,
                    (node) => {
                        const isHiddenInput = node.tagName === 'INPUT' && node.type === 'hidden';
                        if (node.disabled || node.hidden || isHiddenInput)
                            return NodeFilter.FILTER_SKIP;
                        // `.tabIndex` is not the same as the `tabindex` attribute. It works on the
                        // runtime's understanding of tabbability, so this automatically accounts
                        // for any kind of element that could be tabbed to.
                        return node.tabIndex >= 0
                            ? NodeFilter.FILTER_ACCEPT
                            : NodeFilter.FILTER_SKIP;
                    },
                );
                while (walker.nextNode()) nodes.push(walker.currentNode);
                // we do not take into account the order of nodes with positive `tabIndex` as it
                // hinders accessibility to have tab order different from visual order.
                return nodes;
            },
            // @fn focusFirst
            focusFirst (candidates) {
                const previouslyFocusedElement = document.activeElement;
                return candidates.some((candidate) => {
                    // if focus is already where we want to go, we don't want to keep going through the candidates
                    if (candidate === previouslyFocusedElement) return true;
                    candidate.focus();
                    return document.activeElement !== previouslyFocusedElement;
                });
            },
        }"
        {{ $attributes }}
    >
        {{ $slot }}
    </nav>
</x-utils>

<script>
    function debounce(fn, delay) {
        let timeoutId;
        return function(...args) {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(() => {
                fn(...args);
            }, delay);
        };
    }
</script>
