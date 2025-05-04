$(document).ready(function() {
    // Toggle sidebar
    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').toggleClass('active');
    });

    // Load dashboard data
    loadDashboardData();

    // Function to load dashboard data
    function loadDashboardData() {
        fetch('api/dashboard.php')
            .then(response => response.json())
            .then(data => {
                $('#totalMembers').text(data.totalMembers);
                $('#totalTrainers').text(data.totalTrainers);
                $('#totalEquipment').text(data.totalEquipment);
                $('#totalPlans').text(data.totalPlans);
                
                // Load recent members
                let recentMembersHtml = '<table class="table">';
                recentMembersHtml += '<thead><tr><th>Name</th><th>Join Date</th><th>Status</th></tr></thead><tbody>';
                data.recentMembers.forEach(function(member) {
                    recentMembersHtml += `<tr>
                        <td>${member.name}</td>
                        <td>${member.join_date}</td>
                        <td><span class="badge bg-${member.status === 'Active' ? 'success' : 'warning'}">${member.status}</span></td>
                    </tr>`;
                });
                recentMembersHtml += '</tbody></table>';
                $('#recentMembers').html(recentMembersHtml);

                // Load upcoming renewals
                let renewalsHtml = '<table class="table">';
                renewalsHtml += '<thead><tr><th>Member</th><th>Plan</th><th>Expiry Date</th></tr></thead><tbody>';
                data.upcomingRenewals.forEach(function(renewal) {
                    renewalsHtml += `<tr>
                        <td>${renewal.member_name}</td>
                        <td>${renewal.plan_name}</td>
                        <td>${renewal.expiry_date}</td>
                    </tr>`;
                });
                renewalsHtml += '</tbody></table>';
                $('#upcomingRenewals').html(renewalsHtml);
            })
            .catch(error => {
                console.error('Error loading dashboard data:', error);
            });
    }

    // Function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        
        $('#alertContainer').html(alertHtml);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
}); 