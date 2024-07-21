<script>
    function speak(text, speaker = 'Google Bahasa Indonesia', rate = 1.05, pitch = 1, volume = 1) {
        // Create a new instance of SpeechSynthesisUtterance.
        let msg = new SpeechSynthesisUtterance();

        // Set the text.
        msg.text = text;

        // Set the attributes.
        msg.volume = parseFloat(volume);
        msg.rate = parseFloat(rate); //0.6000000238418579
        msg.pitch = parseFloat(pitch); //1.7000000476837158

        // If a voice has been selected, find the voice and set the
        // utterance instance's voice attribute.

        msg.voice = speechSynthesis.getVoices().filter(function(voice) {

            return voice.name == speaker;
        })[0];


        // Queue this utterance.
        window.speechSynthesis.speak(msg);
    }

    const synth = window.speechSynthesis;

    function stop() {
        synth.paused = false;
        synth.pending = false;
        synth.speaking = false;
        synth.cancel();
    }
    stop();
</script>