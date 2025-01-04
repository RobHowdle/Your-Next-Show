<div class="fixed bottom-4 right-4">
  <button id="toggle-notes"
    class="relative flex h-16 w-16 items-center justify-center rounded-full bg-yns_light_gray text-white shadow-lg transition duration-150 ease-in-out hover:bg-yns_yellow">
    <svg id="icon-note" class="absolute h-10 w-10 scale-100 transform transition-transform duration-300"
      xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
      <path fill="#ffffff"
        d="M312 320h136V56c0-13.3-10.7-24-24-24H24C10.7 32 0 42.7 0 56v400c0 13.3 10.7 24 24 24h264V344c0-13.2 10.8-24 24-24zm129 55l-98 98c-4.5 4.5-10.6 7-17 7h-6V352h128v6.1c0 6.3-2.5 12.4-7 16.9z" />
    </svg>
    <svg id="icon-close" class="absolute h-10 w-10 scale-0 transform transition-transform duration-300"
      xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path fill="currentColor"
        d="M12 10.586l6.293-6.293 1.414 1.414L13.414 12l6.293 6.293-1.414 1.414L12 13.414l-6.293 6.293-1.414-1.414L10.586 12 4.293 5.707 5.707 4.293z" />
    </svg>
  </button>
</div>

<div class="fixed bottom-4 right-24 z-50">
  <div id="notes-popout" class="hidden rounded-lg border border-white bg-opac_8_black p-4 shadow-lg">
    <h2 class="font-bold">Add a Note</h2>
    <form id="notes-form" class="mt-4">
      <div class="group mb-4">
        <x-input-label-dark>Note Name</x-input-label-dark>
        <x-text-input id="note-name" name="note-name"></x-text-input>
      </div>
      <div class="group mb-4">
        <x-input-label-dark>Text</x-input-label-dark>
        <x-textarea-input id="note-text"></x-textarea-input>
      </div>

      <div class="group mb-4">
        <x-input-label-dark>Date</x-input-label-dark>
        <x-date-time-input id="note-date" value="{{ now()->format('Y-m-d') }}"></x-date-time-input>
      </div>

      <div class="group mb-4 flex flex-row-reverse justify-end gap-2">
        <x-input-label-dark>Create Todo Item</x-input-label-dark>
        <x-input-checkbox id="convert-to-todo" class="mb-2"></x-input-checkbox>
      </div>

      <button type="submit" id="save-note" class="w-full rounded bg-yns_yellow p-1 text-black">Save Note</button>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const toggleButton = document.getElementById("toggle-notes");
    const newNoteButton = document.getElementById("new-note-button");
    const popout = document.getElementById("notes-popout");
    const iconNote = document.getElementById("icon-note");
    const iconClose = document.getElementById("icon-close");
    const form = document.getElementById("notes-form");

    toggleButton.addEventListener("click", () => {
      if (popout.classList.contains("hidden")) {
        popout.classList.remove("hidden");
        popout.classList.remove("slide-out");
        popout.classList.add("slide-in");

        // Morph icon
        iconNote.classList.remove("scale-100");
        iconNote.classList.add("scale-0");
        iconClose.classList.remove("scale-0");
        iconClose.classList.add("scale-100");
      } else {
        popout.classList.remove("slide-in");
        popout.classList.add("slide-out");

        // Morph icon back
        iconClose.classList.remove("scale-100");
        iconClose.classList.add("scale-0");
        iconNote.classList.remove("scale-0");
        iconNote.classList.add("scale-100");

        setTimeout(() => {
          popout.classList.add("hidden");
        }, 300); // Match this timeout with the animation duration
      }
    });

    if (newNoteButton) {
      newNoteButton.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default link behavior

        // Open the notes popout
        if (popout.classList.contains("hidden")) {
          popout.classList.remove("hidden");
          popout.classList.remove("slide-out");
          popout.classList.add("slide-in");

          // Morph icon
          iconNote.classList.remove("scale-100");
          iconNote.classList.add("scale-0");
          iconClose.classList.remove("scale-0");
          iconClose.classList.add("scale-100");
        }
      });
    }

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      // Handle form submission, e.g., send a POST request to save the note
      const noteData = {
        'name': document.getElementById("note-name").value,
        'text': document.getElementById("note-text").value,
        'date': document.getElementById("note-date").value,
        'isTodo': document.getElementById("convert-to-todo").checked,
      };

      console.log("Sending note data:", noteData); // Log the note data for debugging

      fetch("/dashboard/notes/store-note", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify(noteData),
        })
        .then((response) => {
          if (!response.ok) {
            // Log the status and throw an error if the response is not ok
            console.error("Network response was not ok:", response.statusText);
            throw new Error("Network response was not ok: " + response.statusText);
          }
          return response.json(); // Parse JSON if response is okay
        })
        .then((data) => {
          if (data.success) {
            showSuccessNotification(data.success);

            // Reset form
            form.reset();

            // Hide popout with animations
            popout.classList.remove("slide-in");
            popout.classList.add("slide-out");

            // Morph icon back to the note icon
            iconClose.classList.remove("scale-100");
            iconClose.classList.add("scale-0");
            iconNote.classList.remove("scale-0");
            iconNote.classList.add("scale-100");

            setTimeout(() => {
              popout.classList.add("hidden");
            }, 300); // Match the timeout to the animation duration
          } else {
            showFailureNotification(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showFailureNotification(error);
        });
    });

  });
</script>
