@props([
    'ref',
])
<template
    x-data="{
        activeTriggerId: null,
        indicatorTrackId: null,
        position: null,
        get present () { return !!this.modelValue },
        get isHorizontal () { return this.orientation === 'horizontal' },
        cleanupIndicatorObserver: null,
        cleanupTriggerObserver: null,
        // @fn init
        init () {
            this.indicatorTrackId = this.indicatorTrack.id;
            this.cleanupTriggerObserver = this.resizeObserver(
                'activeTriggerId',
                this.handlePositionChange
            );
            this.cleanupIndicatorObserver = this.resizeObserver(
                'indicatorTrackId',
                this.handlePositionChange
            );
        },
        // @fn destroy
        destroy () {
            this.cleanupIndicatorObserver();
            this.cleanupTriggerObserver();
        },
        // @fn watchPositionAndTrigger
        watchPositionAndTrigger () {
            if (!this.modelValue) {
                this.position = null;
                return;
            }
            const items = this.getItems();
            const item = items.find((item) =>
                item.id.includes(this.modelValue)
            );
            this.activeTriggerId = item.id;
            this.indicatorTrackId = this.indicatorTrack.id;
            this.handlePositionChange();
        },
        // @fn handlePositionChange
        handlePositionChange () {
            if (this.activeTriggerId) {
                const el = document.getElementById(this.activeTriggerId);
                this.position = {
                    size: this.isHorizontal
                        ? el.offsetWidth
                        : el.offsetHeight,
                    offset: this.isHorizontal
                        ? el.offsetLeft
                        : el.offsetTop,
                };
            }
        },
    }"
    x-if="indicatorTrackId"
    x-teleport="#{{ $ref }}"
    x-effect="watchPositionAndTrigger()"
>
    <x-presence>
        <div
            aria-hidden
            :data-state="present ? 'visible' : 'hidden'"
            :data-orientation="orientation"
            :style="{
                position: 'absolute',
                ...(isHorizontal
                    ? {
                        left: 0,
                        width: position?.size + 'px',
                        transform: `translateX(${position?.offset}px)`,
                    }
                    : {
                        top: 0,
                        height: position?.size + 'px',
                        transform: `translateY(${position?.offset}px)`,
                    }),
            }"
            {{ $attributes }}
        >
            {{ $slot }}
        </div>
    </x-presence>
</template>
