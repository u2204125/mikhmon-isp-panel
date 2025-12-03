# Quick Actions - MikroTik Router Management System

A modern, responsive web application for managing MikroTik routers with a sleek UI and secure authentication.

## Features

### Authentication
- **Secure Login System**: JWT-like token-based authentication
- **Persistent Sessions**: 30-day session validity stored in localStorage (cookie-free)
- **Auto-logout**: Automatic logout on token expiration
- **Protected API**: All router management APIs require valid authentication

### Router Management
- **Add Routers**: Create new router configurations with comprehensive settings
- **List Routers**: View all configured routers in a responsive layout
- **Search**: Real-time search across router names, IPs, hotspot names, and DNS

### User Interface
- **Fully Responsive**: Works seamlessly on desktop, tablet, and mobile devices
- **Theme Toggle**: Switch between dark and light themes (preference saved)
- **Toast Notifications**: User-friendly success/error messages
- **Loading States**: Visual feedback during API operations
- **Empty States**: Helpful prompts when no routers exist
- **Accessible**: ARIA labels and keyboard navigation support

## File Structure

```
quick-actions/
├── index.html              # Main application HTML
├── .env.example           # Environment configuration template
├── .env                   # Environment variables (create from .env.example)
├── css/
│   └── styles.css         # Application styles with theme support
├── js/
│   ├── config.js          # Application configuration
│   ├── state.js           # Global state management
│   ├── utils.js           # Utility functions
│   ├── api.js             # API service layer
│   ├── auth.js            # Authentication logic
│   ├── ui.js              # UI management
│   ├── router.js          # Router operations
│   ├── modal.js           # Modal management
│   ├── theme.js           # Theme toggle
│   └── app.js             # Application initialization
└── api/
    ├── config.php         # API configuration and utilities
    ├── login.php          # Authentication endpoint
    └── routers.php        # Router operations endpoint
```

## API Endpoints

### POST /api/login.php
Authenticate user and receive token.

**Request:**
```json
{
  "username": "admin",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "username": "admin",
    "expiresIn": 2592000
  }
}
```

### GET /api/routers.php
Get all routers (requires authentication).

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Routers retrieved successfully",
  "data": [
    {
      "id": "MainOffice",
      "sessname": "MainOffice",
      "ipmik": "192.168.1.1",
      "usermik": "admin",
      "hotspotname": "Office-Hotspot",
      "dnsname": "office.local",
      "currency": "USD",
      "areload": "5",
      "iface": "ether1",
      "idleto": "0",
      "livereport": "enable",
      "status": "active"
    }
  ]
}
```

### POST /api/routers.php
Add a new router (requires authentication).

**Request:**
```json
{
  "sessname": "MainOffice",
  "ipmik": "192.168.1.1",
  "usermik": "admin",
  "passmik": "password",
  "hotspotname": "Office-Hotspot",
  "dnsname": "office.local",
  "currency": "USD",
  "areload": "5",
  "iface": "ether1",
  "idleto": "0",
  "livereport": true
}
```

## Security Features

1. **JWT-like Token Authentication**: Custom implementation without external dependencies
2. **Token Expiration**: 30-day validity with automatic expiration checking
3. **HMAC Signature**: SHA-256 HMAC for token integrity verification
4. **Protected Endpoints**: All router APIs require valid authentication
5. **No Cookies**: Session managed via localStorage for better control
6. **CORS Headers**: Configured for API security
7. **Input Validation**: Required field validation on both client and server

## Router Configuration Fields

| Field | Required | Description |
|-------|----------|-------------|
| Session Name | Yes | Unique identifier for the router |
| IP Address | Yes | MikroTik router IP address |
| Username | Yes | MikroTik admin username |
| Password | Yes | MikroTik admin password |
| Hotspot Name | Yes | Name of the hotspot service |
| DNS Name | No | DNS name for the network |
| Currency | Yes | Currency for billing (USD, EUR, GBP, INR, BDT) |
| Interface | Yes | Network interface (e.g., ether1) |
| Auto Reload | No | Auto reload interval in minutes (default: 5) |
| Idle Timeout | No | Idle timeout in minutes (default: 0) |
| Live Report | No | Enable/disable live reporting |

## Environment Configuration

The application uses environment variables for sensitive configuration. Create a `.env` file from the template:

```bash
cp .env.example .env
```

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `QA_SECRET_KEY` | Secret key for JWT token signing | `quickaction_mikhmon_secure_key_2025` |
| `QA_TOKEN_EXPIRY` | Token expiration time in seconds | `2592000` (30 days) |
| `MIKHMON_CONFIG_PATH` | Path to MikHmon config.php | `../mikhmon/include/config.php` |
| `ROUTEROS_API_PATH` | Path to RouterOS API class | `../mikhmon/lib/routeros_api.class.php` |

**Security Note**: Always use a strong, unique `QA_SECRET_KEY` in production environments.

## Usage

### First Time Setup

1. Open `index.html` in a web browser
2. Login with your MikroTik admin credentials (configured in `mikhmon/include/config.php`)
3. Upon successful login, you'll be redirected to the dashboard

### Adding a Router

1. Click the "Add Router" button
2. Fill in the required fields in the modal form
3. Click "Add Router" to save
4. The router will appear in the list immediately

### Searching

Type in the search box to filter routers by:
- Session name
- IP address
- Hotspot name
- DNS name

### Theme Toggle

Click the sun/moon icon in the header to switch between dark and light themes. Your preference is automatically saved.

## Technical Details

### Storage

- **Authentication Token**: Stored in `localStorage` as `qa_auth_token`
- **Username**: Stored in `localStorage` as `qa_username`
- **Theme Preference**: Stored in `localStorage` as `qa_theme`
- **Router Data**: Persisted in `mikhmon/include/config.php` (parent project)

### Browser Compatibility

- Modern browsers with ES6+ support
- LocalStorage support required
- Fetch API support required

### Responsive Breakpoints

- **Desktop**: > 768px (Table view)
- **Mobile**: ≤ 768px (Card view)

## Integration with Parent Project

The Quick Actions system integrates seamlessly with the existing MikroTik Manager:

1. **Shared Configuration**: Uses the same `config.php` for router storage
2. **Shared Credentials**: Authentication uses admin credentials from parent config
3. **Encryption Compatibility**: Uses the same encrypt/decrypt functions
4. **Non-Intrusive**: All files contained within `quick-actions/` folder

## Troubleshooting

### Login fails
- Verify credentials in `mikhmon/include/config.php`
- Check that encryption functions are loaded correctly
- Ensure API files have proper permissions

### Routers not loading
- Check that `mikhmon/include/config.php` exists and is readable
- Verify authentication token is valid
- Check browser console for API errors

### Changes not persisting
- Ensure `mikhmon/include/config.php` has write permissions
- Check server error logs for PHP errors
- Verify API responses in network tab

## Future Enhancements

- Real-time router connectivity status
- Bulk router import/export
- Advanced search filters
- Router grouping/categorization
- Connection testing before save
- Activity logs and audit trail
- Multi-user support with roles

## License

Integrated with MikroTik Manager (MikHmon) - GNU General Public License v2.0

## Credits

Built as an enhancement to the MikHmon project by Laksamadi Guko.
