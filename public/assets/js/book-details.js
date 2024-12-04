$(document).ready(function() {
    // Get the bookId from the URL query string
    console.log("hello");
    var bookId = new URLSearchParams(window.location.search).get('id');

    // If a bookId is provided, fetch the book details
    if (bookId) {
        fetchBookDetails(bookId);
    } else {
        alert('Book ID is missing in the URL!');
    }
});

function fetchBookDetails(bookId) {
    $.ajax({
        url: 'http://localhost:3000/api/books/' + bookId,  
        type: 'GET',
        success: function(response) {
            if (response) {
                displayBookDetails(response);  // Display the book details from the database
            } else {
                alert('Book not found in the database');
            }
        },
        error: function(error) {
            console.error('Error fetching book details:', error);
            alert('There was an error fetching the book details.');
        }
    });
}

// Function to display the book details in the appropriate sections
function displayBookDetails(book) {
    // Thumbnail
    var thumbnail = book.cover_url || 'default-thumbnail.jpg';
    $('#book-thumbnail').attr('src', thumbnail);

    // Title
    var title = book.title || 'No title available';
    $('#book-title').text(title);

    // Author(s)
    var authors = book.author || 'Unknown author';
    $('#book-author').text('By: ' + authors);

    // Genre
    var genre = book.genre || 'No genre available';
    $('#book-genre').text(genre);

    // Publish Date
    var publishedDate = book.published_date || 'No date available';
    $('#book-published-date').text(publishedDate);

    // Synopsis
    var synopsis = book.synopsis || 'No description available';
    $('#book-synopsis').text(synopsis);

    var averageRating = book.average_rating || 'No rating available';
    $('#book-rating').text('Rating: ' + averageRating);

    // Add "Leave a Review" button functionality
    $('#leave-review-btn').click(function() {
        $('#review-container').toggle(); 
    });

    // Submit review button functionality (basic placeholder action)
    $('#submit-review-btn').click(function() {
        var reviewText = $('#review-text').val();
        if (reviewText) {
            alert('Your review: ' + reviewText);
            $('#review-text').val(''); // Clear the text area after submission
            $('#review-container').hide(); // Hide review container after submission
        } else {
            alert('Please write a review before submitting.');
        }
    });
}
