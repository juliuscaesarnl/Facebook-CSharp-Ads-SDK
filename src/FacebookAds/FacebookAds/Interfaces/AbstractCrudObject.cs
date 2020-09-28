using System;
using System.Collections.Generic;
using Facebook;
using Newtonsoft.Json.Linq;

namespace FacebookAds.Interfaces
{
    /// <summary>
    ///     Abstract create-remote-update-delete Object interface
    /// </summary>
    public abstract class AbstractCrudObject
    {
        /// <summary>
        ///     The ID field identifier
        /// </summary>
        private const string FieldId = "id";

        /// <summary>
        ///     The FIELDS field identifier
        /// </summary>
        private const string FieldFields = "fields";

        /// <summary>
        ///     The data
        /// </summary>
        private readonly Dictionary<string, object> _data = new Dictionary<string, object>();

        /// <summary>
        ///     The identifier
        /// </summary>
        private readonly string _id;

        /// <summary>
        ///     The parent identifier
        /// </summary>
        private readonly string _parentId;

        /// <summary>
        ///     Initializes a new instance of the <see cref="AbstractCrudObject" /> class.
        /// </summary>
        /// <param name="client">The client.</param>
        /// <param name="id">The identifier.</param>
        /// <param name="parentId">The parent identifier.</param>
        /// <exception cref="FacebookApiException">
        ///     An Api instance must be provided as argument or set as instance in
        ///     [FacebookAds.Api]
        /// </exception>
        protected AbstractCrudObject(FacebookClient client, string id, string parentId = null)
        {
            // Set the ID
            if (id != null)
            {
                _id = id;
                _data.Add(FieldId, id); // causing LOTS of conflicts with URL's
            }

            // Set the parent ID
            if (parentId != null) _parentId = id;

            // Check if the client is set
            if (client != null)
                Client = client;
            else
                throw new FacebookApiException(
                    "An Api instance must be provided as argument or set as instance in [FacebookAds.Api]");
        }

        /// <summary>
        ///     Gets the client.
        /// </summary>
        /// <value>
        ///     The client.
        /// </value>
        private FacebookClient Client { get; }

        /// <summary>
        ///     Assures the endpoint is valid.
        /// </summary>
        /// <param name="endpoint">The endpoint.</param>
        /// <returns></returns>
        private string AssureEndpoint(string endpoint = null)
        {
            // Check if endpoint is empty
            if (!string.IsNullOrEmpty(endpoint)) return endpoint;
            endpoint = GetEndpoint();

            // Endpoint still empty?
            if (string.IsNullOrEmpty(endpoint))
                throw new FacebookApiException("Endpoint must be provided via a parameter or GetEndpoint()");
            return endpoint;
        }

        /// <summary>
        ///     Assures that the endpoint string is valid via a Type reference.
        /// </summary>
        /// <param name="reference">The reference.</param>
        /// <returns></returns>
        /// <exception cref="FacebookApiException">Endpoint reference type must be provided via a parameter</exception>
        private string AssureEndpoint(Type reference)
        {
            if (reference == null)
                throw new FacebookApiException("Endpoint reference type must be provided via a parameter");

            var obj = (AbstractCrudObject) Activator.CreateInstance(reference, _id, _parentId, Client);
            return obj.GetEndpoint();
        }

        /// <summary>
        ///     Prepares the data.
        /// </summary>
        /// <param name="fields">The fields.</param>
        /// <param name="parameters">The parameters.</param>
        /// <returns></returns>
        private Dictionary<string, object> AssureData(string[] fields = null,
            Dictionary<string, object> parameters = null)
        {
            _data.Add(FieldFields, fields != null ? string.Join(",", fields) : "");

            if (parameters == null) return _data;
            foreach (var entry in parameters)
                _data.Add(entry.Key, entry.Value);

            return _data;
        }

        /// <summary>
        ///     Gets many objects by connection without a specified endpoint
        /// </summary>
        /// <param name="reference"></param>
        /// <param name="fields">The fields.</param>
        /// <param name="parameters">The parameters.</param>
        /// <returns></returns>
        protected object GetManyByConnection(Type reference, string[] fields = null,
            Dictionary<string, object> parameters = null)
        {
            return JObject.Parse(Client.Get(AssureEndpoint(reference), AssureData(fields, parameters)).ToString());
        }

        /// <summary>
        ///     Gets many objects by connection.
        /// </summary>
        /// <param name="endpoint">The endpoint.</param>
        /// <param name="fields">The fields.</param>
        /// <param name="parameters">The parameters.</param>
        /// <returns>
        ///     Result of Client.Get
        /// </returns>
        protected object GetManyByConnection(string endpoint, string[] fields = null,
            Dictionary<string, object> parameters = null)
        {
            return JObject.Parse(Client.Get(AssureEndpoint(endpoint), AssureData(fields, parameters)).ToString());
        }

        /// <summary>
        ///     Gets the many by connection asynchronous.
        /// </summary>
        /// <param name="reference">The reference.</param>
        /// <param name="fields">The fields.</param>
        /// <param name="parameters">The parameters.</param>
        /// <returns>Result of Client.GetTaskAsync</returns>
        protected object GetManyByConnectionAsync(Type reference, string[] fields = null,
            Dictionary<string, object> parameters = null)
        {
            return JObject.Parse(Client.GetTaskAsync(AssureEndpoint(reference), AssureData(fields, parameters))
                .ToString());
        }

        /// <summary>
        ///     Gets many objects by connection asynchronously.
        /// </summary>
        /// <param name="endpoint">The endpoint.</param>
        /// <param name="fields">The fields.</param>
        /// <param name="parameters">The parameters.</param>
        /// <returns>Result of Client.GetTaskAsync</returns>
        protected object GetManyByConnectionAsync(string endpoint, string[] fields = null,
            Dictionary<string, object> parameters = null)
        {
            return JObject.Parse(Client.GetTaskAsync(AssureEndpoint(endpoint), AssureData(fields, parameters))
                .ToString());
        }

        /// <summary>
        ///     Gets the endpoint of the API call.
        /// </summary>
        /// <returns>
        ///     Endpoint URL
        /// </returns>
        protected abstract string GetEndpoint();
    }
}