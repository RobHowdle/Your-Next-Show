<x-guest-layout>
  <div class="flex h-screen flex-grow items-center justify-center px-2 backdrop-brightness-50">
    <div class="rounded-lg bg-opac_8_black p-6 text-center lg:p-8 xl:p-10 2xl:p-12">
      <h1 class="text-3xl font-bold text-white md:text-4xl xl:text-5xl 2xl:text-6xl">
        Find Your Next Show!
      </h1>
      <p class="my-2 text-base text-white lg:text-lg">
        Search a location below and find a venue in your desired area
      </p>
      <form action="{{ route('venues.filterByCoordinates') }}" method="GET">
        @csrf
        <div class="my-4 flex justify-center">
          <input
            class="search map-input w-full rounded-bl rounded-tl border-b border-l border-r-0 border-t border-white bg-yns_light_gray font-sans text-xl focus:border-white md:w-4/6"
            type="search" id="address-input" name="search_query" placeholder="Search..." />

          <button type="submit" id="search-button"
            class="search-button rounded-br rounded-tr border-b border-r border-t border-white bg-black p-4 text-white hover:bg-gray-800">
            <span class="fas fa-search"></span>
          </button>
        </div>
        <div id="address-map-container" style="width: 100%; height: 400px; display: none;">
          <div style="width: 100%; height: 100%;" id="address-map"></div>
        </div>

        <input style="display: none;" type="text" id="address-latitude" name="latitude" placeholder="Latitude">
        <input style="display: none;" type="text" id="address-longitude" name="longitude" placeholder="Longitude">
      </form>

      <h2 class="text-white">Or</h2>
      <a href="{{ url('/venues') }}" class="flex justify-center text-xl text-white underline">
        Browse all venues
      </a>
    </div>
  </div>

  <div class="flex h-screen flex-grow items-center justify-center px-2 backdrop-brightness-50">
    <img src="{{ asset('storage/images/system/about.jpg') }}" alt="About Image"
      class="absolute bottom-0 right-0 hidden h-[calc(100%+0px)] w-auto object-cover xl:block 3xl:right-32" />

    <div class="relative bg-opac_8_black p-4 text-center lg:p-8 lg:text-left xl:mr-40 xl:max-w-xl 4xl:max-w-3xl">
      <h2 class="mb-4 font-sans text-2xl text-white underline xl:text-3xl">So...what is it?</h2>
      <p class="mb-3 font-sans text-base text-white lg:text-lg">
        Your Next Show is a platform specifically designed and built for bands and artists to be able to find
        their next show. Entering the location you want to search, setting your filters will return a list of venues
        in that area you can perform at. The ability to add and link with promoters in that area and for your type of
        music means that not only are you supporting local businesses, you are being paired with people who have the
        same tastes and interests - increasing the chances of you having a more successful show.
      </p>
      <p class="mb-3 font-sans text-base text-white lg:text-lg">
        We also give you the ability to have a custom dashboard designed and built specifically for you. Whether
        you’re a promoter wanting to keep track of your events and budgets to a designer keeping track of jobs you
        have, all the way to bands managing their gigs through a shareable calendar, you can do it all.
      </p>
      <p class="font-sans text-base text-white lg:text-lg">
        Oh, and did we mention? It is 100% COST and AD FREE.
      </p>
    </div>
  </div>

  <div class="flex min-h-screen flex-col items-center justify-center px-4 backdrop-brightness-50">
    <div class="flex flex-col items-center gap-4 xl:grid xl:grid-cols-2 2xl:gap-12 3xl:gap-14">
      <div class="max-h-auto max-w-[600px] xl:max-w-[400px] 2xl:max-w-[500px]">
        <img src="{{ asset('storage/images/system/idea.jpg') }}" alt="Idea Image" class="object-contain" />
      </div>
      <div class="h-auto max-w-[600px]">
        <div
          class="flex flex-col items-center justify-center gap-3 bg-opac_8_black p-6 text-center text-white md:items-end lg:text-right">
          <h2 class="text-center text-2xl underline lg:text-right xl:text-3xl">Got an idea?</h2>
          <p class="font-sans text-base lg:text-lg">
            Your Next Show is constantly evolving. We hate it when things go stale so we will be regularly releasing new
            features, improving optimisation for all devices and keeping our security the best it can be. We already
            have
            some great ideas but since we have built this platform for specific groups of people it’s important to get
            your thoughts!
            If you have an idea of something you think would be great to add to the platform, you can fill out our form
            and add your suggestion to our ideas board.
          </p>

          <a href="https://form.jotform.com/243454726787369" target="_blank"
            class="rounded bg-white p-2 font-sans text-black transition duration-150 ease-in-out hover:bg-gradient-button hover:text-white">Submit
            Idea</a>
        </div>
      </div>
    </div>

    <div class="mt-4 flex flex-col items-center gap-4 xl:grid xl:grid-cols-2 2xl:gap-12 3xl:gap-14">
      <div
        class="order-2 flex h-auto max-w-[600px] flex-col items-center justify-center gap-3 bg-opac_8_black p-6 text-center text-white md:items-start xl:order-1">
        <h2 class="text-2xl underline xl:text-3xl">Spotted a bug?</h2>
        <p class="text-center font-sans text-base md:text-left lg:text-lg">
          Sometimes the gremlins get in and cause some unexpected errors. Whilst our work is fully tested before it gets
          made live sometimes things do slip through the net. If you find a bug or something that doesn’t seem quite
          right - Let us know!
        </p>
        <a href="https://form.jotform.com/243456695713365" target="_blank"
          class="rounded bg-white p-2 font-sans text-black transition duration-150 ease-in-out hover:bg-gradient-button hover:text-white">
          Report A Bug
        </a>
      </div>
      <div class="max-h-auto order-1 max-w-[600px] justify-self-end xl:order-2 xl:max-w-[400px] 2xl:max-w-[500px]">
        <img src="{{ asset('storage/images/system/bug.jpg') }}" alt="Bug Image" class="object-contain" />
      </div>
    </div>
  </div>


  <div class="flex h-screen flex-col items-center justify-center backdrop-brightness-50">
    <div
      class="flex max-w-screen-2xl flex-col items-center gap-4 rounded-lg bg-opac_8_black p-4 text-center text-white md:p-6 lg:p-8 xl:p-12">
      <h3 class="text-2xl underline xl:text-3xl">Buy Me A Coffee</h3>
      <p class="font-sans text-base lg:text-lg">
        I have always wanted this website to be free for everyone. I don’t like the idea of charging people to help
        them
        find bands and venues, nor do I like the idea of spamming the website with ads to make a couple of quid. This
        ongoing project is <span class="font-bold">entirely self-funded.</span> I will never ask members for financial
        contributions to this website
        or its operations.</p>

      <p class="font-sans text-base lg:text-lg">With that said, I am not blind to the fact that things do cost money,
        and some people
        have
        offered numerous
        times to donate to the website. While this is not necessary, if you feel this website has helped you in any
        way
        and you want to donate a <span class="font-bold">small amount</span> to help with server costs, staffing
        costs,
        upgrades, or even just to say
        thanks, I have created a <span class="font-bold">Buy Me A Coffee</span> link that will allow you to do so.</p>

      <a class="font-sans text-base transition duration-150 ease-in-out hover:text-yns_yellow lg:text-lg"
        href="https://buymeacoffee.com/yournextshow" target="_blank">
        <span class="fas fa-coffee mr-2 text-2xl"></span>Buy Me A Coffee
      </a>

      <p class="font-sans text-base lg:text-lg"><span class="font-bold">PLEASE NOTE:</span> You are not obligated to
        do anything. This
        website will <span class="font-bold">ALWAYS</span> be
        cost <span class="font-bold">AND</span> ad-free for all users.
        If you choose to donate, I thank you from the bottom of my heart.</p>
    </div>
  </div>
</x-guest-layout>

<script>
  function initialize() {
    // console.log("Google Maps API initialized");

    const locationInputs = document.getElementsByClassName("map-input");
    const autocompletes = [];
    const geocoder = new google.maps.Geocoder();

    for (let i = 0; i < locationInputs.length; i++) {
      const input = locationInputs[i];
      const fieldKey = input.id.replace("-input", "");
      const isEdit = document.getElementById(fieldKey + "-latitude").value != '' &&
        document.getElementById(fieldKey + "-longitude").value != '';

      const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || 59.339024834494886;
      const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 18.06650573462189;

      const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
        center: {
          lat: latitude,
          lng: longitude
        },
        zoom: 13,
      });
      const marker = new google.maps.Marker({
        map: map,
        position: {
          lat: latitude,
          lng: longitude
        },
      });

      marker.setVisible(isEdit);

      const autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.key = fieldKey;

      autocompletes.push({
        input,
        map,
        marker,
        autocomplete
      });
    }

    for (let i = 0; i < autocompletes.length; i++) {
      const input = autocompletes[i].input;
      const autocomplete = autocompletes[i].autocomplete;
      const map = autocompletes[i].map;
      const marker = autocompletes[i].marker;

      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        geocoder.geocode({
          placeId: place.place_id
        }, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            const lat = results[0].geometry.location.lat();
            const lng = results[0].geometry.location.lng();
            setLocationCoordinates(autocomplete.key, lat, lng);
          }
        });

        if (!place.geometry) {
          window.alert("No details available for input: '" + place.name + "'");
          input.value = "";
          return;
        }

        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
      });
    }
  }

  function setLocationCoordinates(key, lat, lng) {
    const latitudeField = document.getElementById(key + "-latitude");
    const longitudeField = document.getElementById(key + "-longitude");
    latitudeField.value = lat;
    longitudeField.value = lng;
  }
</script>
<script
  src="https://maps.googleapis.com/maps/api/js?key={{ config('google.maps_api_key') }}&libraries=places&callback=initialize"
  async defer></script>
