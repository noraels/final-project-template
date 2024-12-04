$(document).ready(function() {
    // Search input change handler for dynamic results dropdown
    $('#sidebar-search').on('input', function() {
        var query = $(this).val().trim();
        
        // If the search bar is not empty, show the dropdown and fetch results
        if (query) {
            $('.search-dropdown').show();  
            searchBooks(query);  
        } else {
            // If the search bar is empty, hide the dropdown
            $('.search-dropdown').hide();
        }
    });

    // Handle search button click to trigger search explicitly
    $('#search-btn').click(function() {
        var query = $('#sidebar-search').val().trim();
        if (query) {
            searchBooks(query);  // Call function to search for books
        } else {
            alert('Please enter a search term');
        }
    });

    var libraryName = getLibraryNameFromURL();  
    var encodedLibraryName = encodeURIComponent(libraryName);
    $('#library-name').text(libraryName || 'My Library');  
    updateBookshelf(encodedLibraryName); // Fetch books for the current library
});

function getLibraryNameFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const libraryName = urlParams.get('library');  // Get the library name from URL
    console.log('Library Name:', libraryName);  // Log the library name to check it
    return libraryName;  // Return the library name
}


// Function to search books from Google Books API
function searchBooks(query) {
    $.ajax({
        url: 'https://www.googleapis.com/books/v1/volumes?q=' + query,
        type: 'GET',
        success: function(response) {
            console.log('API Response:', response);  
            displaySearchResults(response.items);  // Display search results
        },
        error: function(error) {
            console.error('Error fetching books:', error);
            alert('An error occurred while fetching books.');
        }
    });
}

// Function to display the search results on the page
function displaySearchResults(books) {
    var resultsContainer = $('#search-results');
    resultsContainer.empty();  // Clear existing results

    if (!books || books.length === 0) {
        resultsContainer.append('<p>No books found.</p>');
        return;
    }

    books.forEach(function(book) {
        var title = book.volumeInfo.title;
        var authors = book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Unknown author';
        var thumbnail = book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'default-thumbnail.jpg';
        console.log(thumbnail);
        var bookId = book.id;  // Get the book ID from the response
        var bookItem = $('<div class="book-item"></div>');
        bookItem.append('<img src="' + thumbnail + '" alt="' + title + '">');
        bookItem.append('<h3><a href="book-details.html?id=' + bookId + '">' + title + '</a></h3>');
        bookItem.append('<p>By ' + authors + '</p>');
        bookItem.append('<button class="add-to-library-btn">Add to Library</button>');
        
        resultsContainer.append(bookItem);

        // When 'Add to Library' is clicked, send the book info to the backend
        bookItem.find('.add-to-library-btn').click(function() {
            var libraryName = getLibraryNameFromURL();  // Fetch the library name dynamically
            addBookToLibrary(book, libraryName);  // Pass the selected library name to the function
        });
    });  
}

function addBookToLibrary(book, libraryName) {
    // Attempt to parse the date using the Date object
    var publishedDate = book.volumeInfo.publishedDate ? new Date(book.volumeInfo.publishedDate) : null;
    
    // Check if the parsed date is valid
    if (publishedDate && !isNaN(publishedDate)) {
        // If it's valid, format it as YYYY-MM-DD
        publishedDate = publishedDate.toISOString().split('T')[0];  // Convert to 'YYYY-MM-DD'
    } else {
        // If invalid, set it to null or a default value
        publishedDate = null;  // Or use a default like 'Unknown Date'
    }
    
    var bookData = {
        title: book.volumeInfo.title, 
        author: book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Unknown author', 
        genre: book.volumeInfo.categories ? book.volumeInfo.categories.join(', ') : 'No category',
        cover_url: book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : null,
        published_date: publishedDate,  // Use the standardized or null date
        synopsis: book.volumeInfo.description ? book.volumeInfo.description : null,
        library_name: libraryName || null  // Use the passed libraryName dynamically
    };

    console.log('Sending this data to backend:', bookData);  // Log the data being sent

    $.ajax({
        url: 'http://localhost:3000/api/books', 
        type: 'POST',
        data: JSON.stringify(bookData),
        contentType: 'application/json',
        success: function(response) {
            console.log("Book added successfully:", response);
            updateBookshelf(libraryName);  // Update the bookshelf for the specific library
        },
        error: function(error) {
            console.error('Error adding book:', error);
            alert('An error occurred while adding the book.');
        }
    });
}

function updateBookshelf(encodedLibraryName) {
    $.ajax({
        url: 'http://localhost:3000/api/books?library_name=' + encodedLibraryName,  // Fetch books with only cover_url
        type: 'GET',
        success: function(response) {
            displayBooks(response);  // Pass the response to display the books
        },
        error: function(error) {
            console.error('Error fetching books:', error);
        }
    });
}

function deleteBook(bookId, bookItem) {
    // Send AJAX request to delete the book
    $.ajax({
        url: 'http://localhost:3000/api/books/' + bookId,  // Endpoint for deleting the book
        type: 'DELETE',
        success: function(response) {
            console.log('Book deleted successfully:', response);

            // Remove the book item from the UI
            bookItem.remove();

            // Redisplay the books after deletion
            var libraryName = getLibraryNameFromURL();  
            var encodedLibraryName = encodeURIComponent(libraryName);
            updateBookshelf(encodedLibraryName);  // Fetch and redisplay books for the current library
        },
        error: function(error) {
            console.error('Error deleting book:', error);
            alert('An error occurred while deleting the book.');
        }
    });
}

function displayBooks(books) {
    var bookshelfContainer = $('#bookshelf-container');
    bookshelfContainer.empty();  // Clear any existing shelves

    var shelf = $('<div class="shelf-background"></div>');  // Create the first shelf with a background image
    var booksContainer = $('<div class="books"></div>');  
    shelf.append(booksContainer);  // Append the books container to the shelf

    books.forEach(function(book, index) {
        var bookItem = $('<div class="book-item"></div>');
        var bookImage = $('<img>').attr('src', book.cover_url || 'default-thumbnail.jpg');  // Handle missing cover URL
        bookItem.append(bookImage);

        // Create buttons (hidden by default, displayed on hover)
        var buttons = $('<div class="buttons"></div>');
        buttons.append('<button class="view-details-btn">View Details</button>');
        buttons.append('<button class="delete-book-btn">Delete Book</button>');
        buttons.append('<button class="hide-cover-btn">Hide Cover</button>');
        bookItem.append(buttons);

        // Append the book item to the container
        booksContainer.append(bookItem);

        // Button Click Handlers
        bookItem.find('.view-details-btn').click(function() {
            viewBookDetails(book.id);  // Show book details
        });

        bookItem.find('.delete-book-btn').click(function() {
            console.log(book.id);
            deleteBook(book.id, bookItem);  // Delete the book item
        });

        bookItem.find('.hide-cover-btn').click(function() {
            hideBookCover(bookItem);  // Hide the book cover image
        });

        // Check if the shelf has reached the limit (5 books or more)
        if ((index + 1) % 5 === 0) {
            bookshelfContainer.append(shelf);

            // Prepare for the next shelf (a new shelf background)
            shelf = $('<div class="shelf-background"></div>');  // Create a new shelf with a background image
            booksContainer = $('<div class="books"></div>');  // Create a new books container for the next shelf
            shelf.append(booksContainer);  // Append the new books container to the new shelf
        }
    });

    // If there are leftover books that didn't fill up the last shelf, add them
    if (books.length % 5 !== 0) {
        bookshelfContainer.append(shelf);  // Add the last shelf with the remaining books
    }
}
// Functions for the buttons
function viewBookDetails(bookId) {
    window.location.href = "book-details.html?id=" + bookId;  // Navigate to the book details page
}

function hideBookCover(bookItem) {
    bookItem.find('img').toggle();  // Toggle visibility of the book cover
}

