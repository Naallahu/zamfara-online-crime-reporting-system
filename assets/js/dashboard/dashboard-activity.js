document.getElementById('activity-filter').addEventListener('change', function(e) {
    const filterValue = e.target.value;
    const activities = document.querySelectorAll('.feed-item');
    
    activities.forEach(item => {
        const type = item.dataset.activityType;
        item.style.display = (filterValue === 'all' || type === filterValue) ? 'flex' : 'none';
    });
});