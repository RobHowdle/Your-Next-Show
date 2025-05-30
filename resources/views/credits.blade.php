<x-guest-layout>
  <div class="flex min-h-screen">
    {{-- Main Content Section --}}
    <div class="relative flex w-full items-center justify-center px-4 py-12">
      {{-- Content Container --}}
      <div class="relative z-10 mt-28 w-full max-w-4xl">
        <div class="mb-8 text-center">
          <h1 class="font-heading text-4xl font-bold text-white md:text-5xl">Credits</h1>
          <p class="mt-4 text-lg text-gray-300">Acknowledging those who helped make this platform possible</p>
        </div>

        <div class="space-y-6 rounded-lg border border-gray-800 bg-gray-900/50 p-6 backdrop-blur-sm md:p-8">
          {{-- Introduction --}}
          <p class="text-gray-200">
            There are many people I'd like to take a moment to thank for making this platform possible—whether through
            playing an active role in development or offering valuable advice and consultation from their own
            experiences. Regardless of how each person contributed, the fact is that they helped, and I am truly
            grateful for their support. </p>

          {{-- Contributors Section --}}
          <div class="rounded-lg bg-black/40 p-6">
            <h2 class="font-heading text-xl font-bold text-white">Special Thanks</h2>
            <p class="mt-2 text-gray-200">I'd like to specifically thank the following individuals for their feedback
              and advice during the build of this platform:</p>
            <ul class="mt-4 grid gap-2 md:grid-cols-2">
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Krispy Harper - KRN Promotions</span>
              </li>
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Jamie-Lee Hannon - Shadow Fest Promotions</span>
              </li>
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Laura Smith - LK Music Promotion</span>
              </li>
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Becky Deane - Lilith Promotions</span>
              </li>
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Jon Northey - Fateweaver</span>
              </li>
              <li class="flex items-center space-x-3">
                <div class="shrink-0 rounded-full bg-yns_yellow p-1">
                  <svg class="h-4 w-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <span class="text-white">Nathan Taylor</span>
              </li>
            </ul>
          </div>

          {{-- Designer Credit --}}
          <div class="rounded-lg bg-black/40 p-6">
            <h2 class="font-heading text-xl font-bold text-white">Design Credit</h2>
            <p class="mt-2 text-gray-200">
              Special credit to <span class="font-bold text-white">Zoe Murphy - Dilluzional Illustration</span>, for her
              amazing design skills and creativity, as well as for patiently dealing with my sometimes quirky requests.
              I am also happy to have her as Your Next Show's exclusive designer.
            </p>
          </div>

          {{-- Community Thanks --}}
          <div class="rounded-lg bg-black/40 p-6">
            <h2 class="font-heading text-xl font-bold text-white">Community Support</h2>
            <p class="mt-2 text-gray-200">
              Finally, a big thank you to all the early adopters in our Discord Server who have been instrumental in
              offering suggestions, sharing their experiences, and contributing their expertise. Your support has been
              invaluable.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
