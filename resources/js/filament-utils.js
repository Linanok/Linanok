document.addEventListener('livewire:init', () => {
    Livewire.on('copy-to-clipboard', async (event) => {
        let value = event[0].value
        await navigator.clipboard.writeText(value)
    });
});
