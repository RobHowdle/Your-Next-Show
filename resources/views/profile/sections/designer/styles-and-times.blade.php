@php
  $workingTimes = isset($profileData['workingTimes']) ? $profileData['workingTimes'] : [];
  $styles = isset($profileData['styles']) ? $profileData['styles'] : [];
  $print = isset($profileData['print']) ? $profileData['print'] : [];
@endphp
<x-design-styles-and-mediums :styles="$styles" :print="$print" :dashboardType="$dashboardType"
  :user="$user"></x-design-styles-and-mediums>
{{-- <x-working-times :workingTimes="$workingTimes" :dashboardType="$dashboardType" :user="$user"></x-working-times> --}}

<script>
  jQuery(document).ready(function() {
    const dashboardType = '{{ $dashboardType }}';
    const userId = '{{ $user->id }}';

    function updateDesignerProfile() {
      // Collect styles data
      let selectedStyles = jQuery('input[name="styles[]"]:checked').map(function() {
        return jQuery(this).val();
      }).get();

      // Collect prints data
      let selectedPrints = jQuery('input[name="prints[]"]:checked').map(function() {
        return jQuery(this).val();
      }).get();

      // Collect working times data
      let workingTimesData = {};
      jQuery('.working-times-input').each(function() {
        let day = jQuery(this).data('day');
        let type = jQuery(this).data('type');
        let value = jQuery(this).val();

        if (!workingTimesData[day]) {
          workingTimesData[day] = {};
        }
        workingTimesData[day][type] = value;
      });



      // Send combined data
      $.ajax({
        url: `/profile/${dashboardType}/designer-profile-update/${userId}`,
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          _method: 'PUT',
          styles: selectedStyles,
          print: selectedPrints,
          working_times: workingTimesData
        },
        success: function(response) {
          showSuccessNotification(response.message);
        },
        error: function(xhr, status, error) {
          showFailureNotification('Error updating designer profile');
          console.error('Error:', error);
        }
      });
    }

    // Add change event listeners
    jQuery('input[name="styles[]"], input[name="prints[]"], .working-times-input').on('change', function() {
      updateDesignerProfile();
    });
  });
</script>
