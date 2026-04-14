<script>
  async function loadListings() {
    try {
      const response = await fetch('/api/listings');
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const listings = await response.json();

      const apartmentsContainer = document.getElementById('apartments');
      const housesContainer = document.getElementById('houses');

      // Clear previous listings if any
      apartmentsContainer.innerHTML = '<h2>Apartments</h2>';
      housesContainer.innerHTML = '<h2>Houses</h2>';

      listings.forEach(item => {
        const div = document.createElement('div');
        div.className = 'listing';
        div.innerHTML = `
          <h3>${item.title}</h3>
          <p>Rooms: ${item.rooms} | Bathrooms: ${item.bathrooms} | Pool: ${item.pool ? 'Yes' : 'No'} | Furnished: ${item.furnished ? 'Yes' : 'No'}</p>
          <p>Parking: ${item.parking ? 'Yes' : 'No'} | Braai Area: ${item.braai ? 'Yes' : 'No'} | Laundry Area: ${item.laundry ? 'Yes' : 'No'}</p>
          <p>Rental: R${item.rental}/month | Property Value: R${item.value.toLocaleString()}</p>
        `;
        if (item.type === 'apartment') 
          apartmentsContainer.appendChild(div)
         else if (item.type === 'house') 
          housesContainer.appendChild(div)
        
      );
     catch (error) {
      console.error('Failed to load listings:', error)
    }
  

  document.addEventListener('DOMContentLoaded', loadListings);
</script>