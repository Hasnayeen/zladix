<template
    x-data="{
        state: null,
        dispatch: null,
        node: null,
        prevAnimationName: 'none',
        styles: {},
        // @fn isPresent
        get isPresent () { return ['mounted', 'unmountSuspended'].includes(this.state) },
        // @fn init
        init () {
            this.state = this.present ? 'mounted' : 'unmounted';
            this.dispatch = this.useStateMachine(
                this.state,
                {
                    mounted: {
                        UNMOUNT: 'unmounted',
                        ANIMATION_OUT: 'unmountSuspended',
                    },
                    unmountSuspended: {
                        MOUNT: 'mounted',
                        ANIMATION_END: 'unmounted',
                    },
                    unmounted: {
                        MOUNT: 'mounted',
                    },
                }
            );
            this.$watch('present', (currentPresent, prevPresent) => {
                const hasPresentChanged = prevPresent !== currentPresent;
                {{-- await $nextTick(); --}}
                if (hasPresentChanged) {
                    const currentAnimationName = this.getAnimationName(this.node);

                    if (currentPresent) {
                        this.state = this.dispatch('MOUNT');
                    } else if (
                        currentAnimationName === 'none' ||
                        this.styles?.display === 'none'
                    ) {
                        // If there is no exit animation or the element is hidden, animations won't run
                        // so we unmount instantly
                        this.state = this.dispatch('UNMOUNT');
                    } else {
                        /**
                        * When `present` changes to `false`, we check changes to animation-name to
                        * determine whether an animation has started. We chose this approach (reading
                        * computed styles) because there is no `animationrun` event and `animationstart`
                        * fires after `animation-delay` has expired which would be too late.
                        */
                        const isAnimating = this.prevAnimationName !== currentAnimationName;
                        if (prevPresent && isAnimating) {
                            this.state = this.dispatch('ANIMATION_OUT');
                        } else {
                            this.state = this.dispatch('UNMOUNT');
                        }
                    }
                }
                {{-- console.log('watch present', this.isPresent); --}}
            });
        },
        // @fn destroy
        destroy () {
            /**
            * Triggering an ANIMATION_OUT during an ANIMATION_IN will fire an `animationcancel`
            * event for ANIMATION_IN after we have entered `unmountSuspended` state. So, we
            * make sure we only trigger ANIMATION_END for the currently active animation.
            */
            const handleAnimationEnd = (event) => {
                const currentAnimationName = this.getAnimationName(this.node);
                const isCurrentAnimation = currentAnimationName.includes(event.animationName);
                if (event.target === this.node && isCurrentAnimation) {
                    this.state = this.dispatch('ANIMATION_END');
                }
            };
            const handleAnimationStart = (event) => {
                if (event.target === this.node) {
                    // if animation occurred, store its name as the previous animation.
                    this.prevAnimationName = this.getAnimationName(this.node);
                }
            };
            this.$watch(
                'node',
                (newNode, oldNode) => {
                    if (newNode) {
                        this.styles = getComputedStyle(newNode);
                        newNode.addEventListener('animationstart', handleAnimationStart);
                        newNode.addEventListener('animationcancel', handleAnimationEnd);
                        newNode.addEventListener('animationend', handleAnimationEnd);
                    } else {
                        // Transition to the unmounted state if the node is removed prematurely.
                        // We avoid doing so during cleanup as the node may change but still exist.
                        this.dispatch('ANIMATION_END');

                        oldNode?.removeEventListener('animationstart', handleAnimationStart);
                        oldNode?.removeEventListener('animationcancel', handleAnimationEnd);
                        oldNode?.removeEventListener('animationend', handleAnimationEnd);
                    }
                }
            );
            this.$watch('state', () => {
                const currentAnimationName = this.getAnimationName(this.node);
                this.prevAnimationName = this.state === 'mounted' ? currentAnimationName : 'none';
            });
        },
        // @fn getAnimationName
        getAnimationName (node) {
            return node ? getComputedStyle(node).animationName || 'none' : 'none';
        },
    }"
    x-if="present || isPresent"
>
    {{ $slot }}
</template>
